<?php

namespace App\Http\Controllers\API\Authentication;


use App\Models\Doctor;
use App\Traits\ImageTrait;
use App\Models\SocialAccount;
use Doctrine\Common\Lexer\Token;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\DoctorLoginRequest;
use GuzzleHttp\Exception\ClientException;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Requests\DoctorRegisterRequest;

class DoctorAuthenticationController extends Controller
{
    use ImageTrait;  // Store image

//------------------------------Default Authentication Methods----------------------------------//

    // DoctorRegisterRequest contain registration rules for Doctor
    public function register(DoctorRegisterRequest $request) {

        $file_name = $this->saveImage($request->image, 'images/profileImages');

        //create doctor
        $doctor = Doctor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $file_name,
            'phone' => $request->phone,
        ]);

        //create token
        $token = $doctor->createToken('doctor_token');

        return response([
            'token' => $token
        ]);
    }

    public function login(DoctorLoginRequest $request) {

        $doctor = Doctor::where('email' , $request->email)->first();

        //check if doctor is not found or password not matched with password in DB
        if (!$doctor|| !Hash::check($request->password, $doctor->password))
        {
            return response([
                'message' => 'Email or Password may be wrong, please try again'
            ]);
        }

        //create token
        $token = $doctor->createToken('doctor_token');

        return response([
            'token' => $token
        ]);
    }

    public function logout() {

        if(Auth::guard('doctor')->check()){
            $accessToken = Auth::guard('doctor')->user()->token();

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

    //Redirect Doctor to the Provider authentication page
    public function redirectToProvider($provider){
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    //Get Doctor information from Provider.
    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $doctor = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response([
                'message' => 'Invalid credentials provided'
            ]);
        }

        $doctorCreated = Doctor::firstOrCreate(
            [
                'email' => $doctor->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $doctor->getName(),
                'status' => true,
            ]
        );
        $doctorCreated->providers()->updateOrCreate(
            [
                'provider_name' => $provider,
                'provider_id' => $doctor->getId(),
            ],
            [
                'avatar' => $doctor->getAvatar()
            ]
        );
        $token = $doctorCreated->createToken('token-name')->plainTextToken;

        return response([
            'message' => $doctorCreated,
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
        $doctor = Doctor::where('id' , Auth::id())->first();
        return $doctor;
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $file_name = $this->saveImage($request->image, 'images/profileImages');

        //create Doctor
        $doctor -> update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $file_name,
            'phone' => $request->phone,
        ]);

        //create token
        $token = $doctor->createToken('doctor_token');

        return response([
            'message' => 'Profile information has been updated successfully',
            'token' => $token
        ]);
    }

    public function destroy(){
        $doctor = Doctor::where('id' , Auth::id());
        $doctor->delete();
        return response([
            'mesaage' => 'Your account has been deleted'
        ]);
    }

//------------------------------End Profile Methods--------------------------------//



}
