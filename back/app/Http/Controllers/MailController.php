<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use App\Http\Requests\StoreMailRequest;
use App\Http\Requests\UpdateMailRequest;

class MailController extends Controller{
    public function index(){return Mail::all();}
    public function show(Mail $mail){return $mail;}
    public function store(StoreMailRequest $request){return Mail::create($request->all());}
    public function update(UpdateMailRequest $request, Mail $mail){$mail->update($request->all());return $mail;}
    public function destroy(Mail $mail){$mail->delete();return $mail;}
}
