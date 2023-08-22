<?php

namespace App\Http\Controllers\API\Patient;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

class PatientController extends Controller
{
    // display some doctors in user home page
    public function index(){
        $doctors = Doctor::paginate(8);
        return $doctors;
    }

    //display specific doctor information page when patient click on doctor profile
    public function show($id){
        $doctor = Doctor::findOrFail($id);
        return $doctor;
    }
}
