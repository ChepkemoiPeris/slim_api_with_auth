<?php
namespace App\Controllers;
use App\Response\CustomResponse;
use App\Requests\CustomRequestHandler;
use App\Models\Guest;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Rules\NotEmpty;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
class GuestController{
    protected $customResponse;
    protected $guest;
    protected $validator;
    public function __construct(){
        $this->customResponse = new CustomResponse;
        $this->guest = new Guest;
        $this->validator = new Validator;
    }
    public function createGuest(Request $request,Response $response){
        $this->validator->validate($request,[
            'name'=>v::notEmpty(),
            'email'=>v::notEmpty()->email(),
            'comment'=>v::notEmpty()
        ]);
        if($this->validator->failed()){
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response,$responseMessage);
        }
        $this->guest->create([
            "full_name"=> CustomRequestHandler::getParam($request,"name"),
            "email"=> CustomRequestHandler::getParam($request,"email"),
            "comment"=> CustomRequestHandler::getParam($request,"comment")
        ]);
        $responseMessage ="Guest created successfully";
        $this->customResponse->is200Response($response,$responseMessage);
    
    }
    public function viewGuests(Request $request,Response $response){ 
        $responseMessage = $this->guest->get();
        $this->customResponse->is200Response($response,$responseMessage);
    
    }
    public function getSingleGuest(Request $request,Response $response,$id){ 
        $responseMessage = $this->guest->where('id',$id)->get(); 
        $this->customResponse->is200Response($response,$responseMessage);
    }
    public function editGuest(Request $request,Response $response,$id){ 
        $this->validator->validate($request,[
            'name'=>v::notEmpty(),
            'email'=>v::notEmpty()->email(),
            'comment'=>v::notEmpty()
        ]);
        if($this->validator->failed()){
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response,$responseMessage);
        }
      $this->guest->where('id',$id)->update(
        [
            "full_name"=> CustomRequestHandler::getParam($request,"name"),
            "email"=> CustomRequestHandler::getParam($request,"email"),
            "comment"=> CustomRequestHandler::getParam($request,"comment")
        ]
      ); 
        $responseMessage ="Guest record updated successfully";
        $this->customResponse->is200Response($response,$responseMessage);
    }
    public function deleteGuest(Request $request,Response $response,$id){
        $responseMessage = $this->guest->where('id',$id)->delete(); 
        $responseMessage ="Guest record deleted successfully";
        $this->customResponse->is200Response($response,$responseMessage);
    }
    public function countGuests(Request $request,Response $response){
        $responseMessage = $this->guest->count();  
        $this->customResponse->is200Response($response,$responseMessage);  
    }
}