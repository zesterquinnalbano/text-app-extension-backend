<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */
use Illuminate\Http\Request;
// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

$router->post('login', 'AuthenticationController@login');

$router->group(['middleware' => 'auth'], function ($router) {
    $router->group(['prefix' => 'auth'], function ($router) {
        $router->post('logout', 'AuthenticationController@logout');
        $router->post('refresh', 'AuthenticationController@refresh');
        $router->get('user', 'AuthenticationController@user');
        $router->patch('update-user', 'AuthenticationController@update');
    });

    $router->group(['prefix' => 'contacts'], function ($router) {
        $router->get('/', 'ContactController@index');
        $router->post('store', 'ContactController@store');
        $router->get('{id}/edit', 'ContactController@edit');
        $router->patch('{id}/update', 'ContactController@update');
        $router->post('{id}/destroy', 'ContactController@destroy');
        $router->get('search', 'ContactController@search');
    });

    $router->group(['prefix' => 'conversations'], function ($router) {
        $router->get('/', 'ConversationController@index');
        $router->post('send', 'ConversationController@store');
        $router->post('send-new', 'ConversationController@sendNew');
        $router->get('{id}', 'ConversationController@show');
        $router->patch('{id}/update-new-message', 'ConversationController@update');
        $router->post('{id}/destroy', 'ConversationController@destroy');
    });

    $router->get('contact-groups', 'ContactGroupController');

    $router->group(['prefix' => 'twilio-numbers'], function ($router) {
        $router->get('/', 'TwilioNumberController');
    });

});

$router->group(['prefix' => 'twilio'], function ($router) {
    $router->post('recieve-message', 'ConversationController@recieve');
    $router->post('update-message-status', 'ConversationController@updateMessageStatus');
});

$router->post('contacts/import', function (Request $request) {
    return response()->json(['data' => $request->toArray()]);

});
