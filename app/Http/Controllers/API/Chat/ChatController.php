<?php

namespace App\Http\Controllers\API\Chat;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\Chat;
use App\Models\Message;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;

class ChatController extends Controller
{
    public $user;

    //start new chat method
    public function create(MessageRequest $request){

        $chat_id=Chat::where('receiver_name',$request->receiver_name)->first();

        //check if chat already exist then we don't need to create one
        if ( isset($chat_id)) {
             //start creatend new message
             $message =  Message::create([
                'chat_id' => $chat_id->id,
                'sender_name' => Auth::user()->name,
                'receiver_name' => $request->receiver_name,
                'content' => $request->content,
                'status' => null,
            ]);

            return response([
                'message' => $message,
            ]);


        }else{
            //create chat instance
            $chat = Chat::create([
                'sender_name' => Auth::user()->name,
                'receiver_name' => $request->receiver_name,
                'last_seen' => null,
                ]);

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_name' => Auth::user()->name,
                'receiver_name' => $request->receiver_name,
                'content' => $request->content,
                'status' => null,
            ]);

            return response([
                'message' => $message,
            ]);
        }



    }



    //show all messages in the chat
    public function showMessages(Request $request){
        $chat_messages = Message::all()->where('receiver_name',$request->receiver_name);
        return response([
            'message' => $chat_messages
        ]);
    }


    //show all chats user have
    public function showChats(){
        $chat = Chat::all()->where('sender_name',Auth::user()->name);
        return response([
            'message' => $chat
        ]);
    }
}
