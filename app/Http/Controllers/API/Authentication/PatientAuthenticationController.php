<?php

namespace App\Http\Controllers\API\Authentication;

use App\Models\Patient;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;
use App\Http\Requests\PatientLoginRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Requests\PatientRegisterRequest;

class PatientAuthenticationController extends Controller
{
    use ImageTrait;  // Store image


//------------------------------Default Authentication Methods----------------------------------//

    // PatientRegisterRequest contain registration rules for patient
    public function register(PatientRegisterRequest $request) {

        $file_name = $this->saveImage($request->image, 'images/profileImages');

        //create Patient
        $patient = Patient::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $file_name,
            'phone' => $request->phone,
        ]);

        //create token
        $token = $patient->createToken('patient_token');

        return response([
            'token' => $token
        ]);
    }

    public function login(PatientLoginRequest $request) {

        $patient = Patient::where('email' , $request->email)->first();

        //check if patient is not found or password not matched with password in DB
        if (!$patient|| !Hash::check($request->password, $patient->password))
        {
            return response([
                'message' => 'Email or Password may be wrong, please try again'
            ]);
        }

        //create token
        $token = $patient->createToken('patient_token');

        return response([
            'token' => $token
        ]);
    }

    public function logout() {

        if(Auth::guard('patient')->check()){
            $accessToken = Auth::guard('patient')->user()->token();

                DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);
            $accessToken->revoke();
        return response([
            'mesaage' => 'Logged out sucsessfully'
        ]);
        }
    }

//------------------------------End Default Authentication Methods----------------------------------//






//------------------------------Authentication By Social Network Methods--------------------------------//

    //Redirect Patient to the Provider authentication page
    public function redirectToProvider($provider){
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    //Get Patient information from Provider.
    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $patient = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response([
                'message' => 'Invalid credentials provided'
            ]);
        }

        $patientCreated = Patient::firstOrCreate(
            [
                'email' => $patient->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $patient->getName(),
                'status' => true,
            ]
        );
        $patientCreated->providers()->updateOrCreate(
            [
                'provider_name' => $provider,
                'provider_id' => $patient->getId(),
            ],
            [
                'avatar' => $patient->getAvatar()
            ]
        );
        $token = $patientCreated->createToken('token-name')->plainTextToken;

        return response([
            'message' => $patientCreated,
            'token' => $token
        ]);
    }

    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'apple', 'google'])) {
            return response([
                'message' => 'Please login using facebook, apple or google'
            ]);
        }
    }

//------------------------------End Authentication By Social Network Methods--------------------------------//






//------------------------------Profile Methods--------------------------------//

    public function show() {
        $patient = Patient::where('id' , Auth::id())->first();
        return $patient;
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $file_name = $this->saveImage($request->image, 'images/profileImages');

        //create Patient
        $patient -> update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $file_name,
            'phone' => $request->phone,
        ]);

        //create token
        $token = $patient->createToken('patient_token');

        return response([
            'message' => 'Profile information has been updated successfully',
            'token' => $token
        ]);
    }

    public function destroy(){
        $patient = Patient::where('id' , Auth::id());
        $patient->delete();
        return response([
            'mesaage' => 'Your account has been deleted'
        ]);
    }
//------------------------------End Profile Methods--------------------------------//


}
