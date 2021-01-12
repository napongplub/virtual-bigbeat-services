<?php

use Illuminate\Support\Facades\Route;
use App\Model\Register;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(["prefix" => "timezones"], function () {
    Route::get("/", "API\TimezoneController@index");
    Route::get("/{id}", "API\TimezoneController@get");
});

Route::group(['middleware' => 'cors', 'prefix' => 'register'], function () {
    Route::post("create", "API\RegisterController@store");
    Route::post("login", "API\RegisterAuthentication@login");
    Route::post("login-speaker", "API\RegisterAuthentication@loginSpeaker");
    Route::post("logout", "API\RegisterAuthentication@logout");
    Route::post('saveBrochure', 'API\RegisterController@saveBrochure');
    Route::get("form-init", 'API\RegisterController@getInitForm');
    Route::get('countries', 'API\RegisterController@getCountries');
    Route::get('nature-of-business', 'API\RegisterController@getNatureOfBusiness');
    Route::get('job-level', 'API\RegisterController@getJobLevel');
    Route::get('job-function', 'API\RegisterController@getJobFunction');
    Route::get('role-process', 'API\RegisterController@getRoleProcess');
    Route::get('number-of-employees', 'API\RegisterController@getNumberOfEmployees');
    Route::get('reason-for-attending', 'API\RegisterController@getReasonForAttending');
    Route::get('find-out-about-bct', 'API\RegisterController@getFindOutAbout');
    Route::get('prefix-name', 'API\RegisterController@getPrefix');
    Route::get('budget', 'API\RegisterController@getBudget');
    Route::get('decrypt_str', 'API\RegisterController@decrypt_str');
    Route::post('getBrochureBag', 'API\RegisterController@getBrochureBag');
    Route::post('forgetPassword', 'API\RegisterController@forgetPasswordMethod1');
    Route::get('testSendEmail', 'API\RegisterController@testSendEmail');
    Route::post('getHash', 'API\RegisterController@getHash');
    Route::post('update-image-profile', "API\RegisterController@updateImageProfile");
    Route::post('interests', 'API\RegisterController@getInterests');
    Route::get('network-lounge', 'API\RegisterController@networkLounge');

    Route::group(['middleware' => ['jwt.verify:register']], function () {
        Route::put('update-profile/{register}', 'API\RegisterController@update');
        Route::patch('update-information', "API\RegisterController@updateInformation");
        Route::get("me", "API\RegisterAuthentication@me");
        Route::get('getReceiverNetworkLounge/{id}', 'API\RegisterController@getReceiverNetworkLounge');
    });
});

Route::group(['prefix' => 'exhibitor'], function () {
    Route::post('login', 'API\ExhibitorAuthentication@login');
    Route::post('logout', 'API\ExhibitorAuthentication@logout');

    Route::get('/', 'API\ExhibitorController@index');
    Route::get('getCategoryList', 'API\ExhibitorController@getCategoryList');

    Route::get('getCategoryListByMainCate', 'API\ExhibitorController@getCategoryListByMainCate');
    Route::get('getExhibitorList', 'API\ExhibitorController@getExhibitorList');
    Route::get('getExhibitorListSideHall', 'API\ExhibitorController@getExhibitorListSideHall');

    Route::get('getExhibitorListDirectory', 'API\ExhibitorController@getExhibitorListDirectory');

    Route::post('getExhibitorById', 'API\ExhibitorController@getExhibitorById');
    Route::post('getExhibitorByCategoryId', 'API\ExhibitorController@getExhibitorByCategoryId');

    Route::post('getSubCategory', 'API\ExhibitorController@getSubCategory');
    Route::post('getSubCategoryBySubCategoryId', 'API\ExhibitorController@getSubCategoryBySubCategoryId');
    Route::get('getSubCategoryList', 'API\ExhibitorController@getSubCategoryList');
    Route::get('getSubCategoryListByMainCateList', 'API\ExhibitorController@getSubCategoryListByMainCateList');

    Route::post('getExhibitorbyMainCategory', 'API\ExhibitorController@getExhibitorbyMainCategory');
    Route::post('getExhibitorByMultiCategory', 'API\ExhibitorController@getExhibitorByMultiCategory');
    Route::get('getInterestCateByExhibitorId', 'API\ExhibitorController@getInterestCateByExhibitorId');
    Route::get('getExhibitorByCategoryPage', 'API\ExhibitorController@getExhibitorByCategoryPage');
    Route::post('getData', 'API\ExhibitorController@getData');

    Route::get('getCountriesExhibitor', 'API\ExhibitorController@getCountriesExhibitorHaving');

    Route::get('network-lounge', 'API\ExhibitorController@networkLounge');

    Route::post('interests', 'API\ExhibitorController@getInterests');

    Route::post("update/portal/{id}", "API\ExhibitorController@updatePortal"); // test not check authentication

    Route::post("active/portal/video/{id}/{dataId}", "API\ExhibitorController@activeVideo");

    // Route::get("get/portal/video/{id}/{dataId}", "API\ExhibitorController@getVideo");
    Route::get("get/portal/eposter/{id}/{dataId}", "API\ExhibitorController@getEposter");
    Route::get("get/portal/promotion/{id}/{dataId}", "API\ExhibitorController@getPromition");
    Route::get("get/portal/brochure/{id}/{dataId}", "API\ExhibitorController@getBrochure");

    // Route::post("add/portal/video/{id}", "API\ExhibitorController@addVideo");
    Route::post("add/portal/eposter/{id}", "API\ExhibitorController@addEposter");
    Route::post("add/portal/promotion/{id}", "API\ExhibitorController@addPromition");
    Route::post("add/portal/brochure/{id}", "API\ExhibitorController@addBrochure");

    Route::post("update/portal/video/{id}/{dataId}", "API\ExhibitorController@updateVideo");
    Route::post("update/portal/eposter/{id}/{dataId}", "API\ExhibitorController@updateEposter");
    Route::post("update/portal/promotion/{id}/{dataId}", "API\ExhibitorController@updatePromition");
    Route::post("update/portal/brochure/{id}/{dataId}", "API\ExhibitorController@updateBrochure");

    // Route::post("del/portal/video/{id}/{dataId}", "API\ExhibitorController@delVideo");
    Route::post("del/portal/eposter/{id}/{dataId}", "API\ExhibitorController@delEposter");
    Route::post("del/portal/promotion/{id}/{dataId}", "API\ExhibitorController@delPromition");
    Route::post("del/portal/brochure/{id}/{dataId}", "API\ExhibitorController@delBrochure");

    Route::group(['middleware' => ['jwt.verify:exhibitor']], function () {
        Route::get("me", "API\ExhibitorAuthentication@me");
        Route::post("update/{id}", "API\ExhibitorController@update");
        Route::get("profile", "API\ExhibitorController@getExhibitorProfile");



        Route::get("video", "API\ExhibitorController@getVideo");
        Route::get("video/{id}", "API\ExhibitorController@getVideoById");
        Route::post("add-video", "API\ExhibitorController@addVideo");
        Route::delete("delete-video/{id}", "API\ExhibitorController@deleteVideo");
        Route::post("active-video/{id}", "API\ExhibitorController@activeVideo");
        Route::patch("update-video/{id}", "API\ExhibitorController@updateVideo");

         // exhibitor > ma: e-poster
         Route::get("poster", "API\ExhibitorController@getPoster");
         Route::get("poster/{id}", "API\ExhibitorController@getPosterById");
         Route::post("add-poster", "API\ExhibitorController@uploadPoster");
         Route::delete("delete-poster/{id}", "API\ExhibitorController@deletePoster");
         Route::post("update-poster/{id}", "API\ExhibitorController@updatePoster");

         // exhibitor > ma: promotion
         Route::get("promotion", "API\ExhibitorController@getPromotion");
         Route::get("promotion/{id}", "API\ExhibitorController@getPromotionById");
         Route::post("add-promotion", "API\ExhibitorController@uploadPromotion");
         Route::delete("delete-promotion/{id}", "API\ExhibitorController@deletePromotion");
         Route::post("update-promotion/{id}", "API\ExhibitorController@updatePromotion");

         // exhibitor > ma: brochure
         Route::get("brochure", "API\ExhibitorController@getBrochure");
         Route::get("brochure/{id}", "API\ExhibitorController@getBrochureById");
         Route::post("add-brochure", "API\ExhibitorController@uploadBrochure");
         Route::delete("delete-brochure/{id}", "API\ExhibitorController@deleteBrochure");
         Route::post("update-brochure/{id}", "API\ExhibitorController@updateBrochure");





        Route::patch('update-information', "API\ExhibitorController@updateInformation");
        Route::post('update-image-profile', "API\ExhibitorController@updateImageProfile");
        Route::post('update-booth-banner', "API\ExhibitorController@updateBoothBanner");
        Route::post('update-booth-logo', "API\ExhibitorController@updateBoothLogo");
        // Route::apiResource("/", "API\ExhibitorController");

        Route::get('getDataAccount/{id}/{type}', 'API\ExhibitorController@getDataAccount');
    });
});

Route::group(['prefix' => 'backoffice'], function () {
    Route::post('login', 'API\BackofficeAuthentication@login');
    Route::post('logout', 'API\BackofficeAuthentication@logout');
    // Route::get('allregister', 'API\RegisterController@getallregister');
    Route::group(['middleware' => ['jwt.verify:backoffice']], function () {
        Route::get('me', 'API\BackofficeAuthentication@me');
        Route::post('import', 'API\ExhibitorController@importData');
        Route::post('makeApprove', 'API\RegisterController@makeApproveRegister');
        Route::apiResource("register", "API\RegisterController");
        Route::get('register_buyer', 'API\RegisterController@getBuyer');
        Route::apiResource('exhibitor', "API\ExhibitorController");
        Route::get('exhibitor_tb', 'API\ExhibitorController@exhibitor_tb');
        Route::get("export/register", 'API\RegisterController@exportExcel');
        Route::get("export/buyer", 'API\RegisterController@exportExcelBuyer');
        Route::get("export/register_remind", 'API\RegisterController@exportExcel_remind');
        Route::post('sendReminder', 'API\RegisterController@sendReminder');
        Route::post('sendNotifyVisitor', 'API\RegisterController@sendNotifyVisitor');
        Route::post('sendNotifyExhibitor', 'API\ExhibitorController@sendNotifyExhibitor');

        Route::get("allIdVisitor", 'API\RegisterController@allIdVisitor');
        Route::get("allIdExhibitor", 'API\ExhibitorController@allIdExhibitor');
    });

});

Route::group(['prefix' => 'matching'], function () {

    Route::post('getSlot', 'API\MatchingController@getSlot');
    Route::post('updateSlot', 'API\MatchingController@updateSlot');
    Route::post('getAppointmentDetail', 'API\MatchingController@getAppointmentDetail');
    Route::post('createAppointment', 'API\MatchingController@createAppointment');
    Route::post('getAppointmentList', 'API\MatchingController@getAppointmentList');
    Route::post('updateAppointment', 'API\MatchingController@updateAppointment');
    Route::post('getBookingList', 'API\MatchingController@getBookingList');
    Route::post('getProfile', 'API\MatchingController@getProfile');
    Route::post('getAppointmentProfile', 'API\MatchingController@getAppointmentProfile');
    Route::get('getCountriesExhibitor', 'API\MatchingController@getCountriesExhibitorHaving');
    Route::get('getCountriesBuyer', 'API\MatchingController@getCountriesBuyerHaving');
    Route::post('getRequestList', 'API\MatchingController@getRequestList');

    Route::post('checkDuplicateRequest', 'API\MatchingController@checkDuplicateRequest');
    Route::post('cancelRequest', 'API\MatchingController@cancelRequest');
    Route::post('cancelAccept', 'API\MatchingController@cancelAccept');
    Route::post('reAppointment', 'API\MatchingController@reAppointment');

    Route::post('testSendMail', 'API\MatchingController@testSendMail');
    Route::post('getExhibitorBackoffice', 'API\MatchingController@getExhibitorBackoffice');
    Route::post('getRegisterBackoffice', 'API\MatchingController@getRegisterBackoffice');
    Route::post('getAppointmentBackoffice', 'API\MatchingController@getAppointmentBackoffice');
    Route::get("export/appointment", 'API\MatchingController@exportExcel');

    Route::get("export/appointmentCancel", 'API\MatchingController@exportExcelCancel');
    Route::get("export/appointmentAll", 'API\MatchingController@exportExcelAll');


    // send email
    // -> impact invite register to buyer
    Route::post('email-invite-to-buyer', 'API\EmailController@inviteRegisterToBuyer'); // /matching/email-invite-to-buyer
    Route::post('email-request-req-a',  'API\EmailController@requestReqA');
    Route::post('email-request-req-b', 'API\EmailController@requestReqB');
    Route::post('email-request-res-a', 'API\EmailController@requestResA');
    Route::post('email-request-res-b', 'API\EmailController@requestResB');
    Route::post('email-request-bizmat-a', 'API\EmailController@requestBusinessMatchingTest');

    Route::get('have-slot/{id}', 'API\MatchingController@haveSlot');
});

Route::group(["prefix" => 'video'], function () {
    Route::get('create-session', 'API\VideoChatController@createSession');
    Route::post('createVideoMatchingRoom', 'API\VideoChatController@createVideoMatchingRoom');
    Route::post('createRating', 'API\VideoChatController@createRating');
    Route::post('createRatingBack', 'API\VideoChatController@createRatingBack');
    Route::post('getBoothVideoCallList', 'API\VideoChatController@getBoothVideoCallList');

    Route::get('get-visitor/{id}', 'API\VideoChatController@getVisitorById');
});

Route::group(['prefix' => 'syslog'], function () {
    Route::post('visitLog', 'API\SysLogController@visitLog');

    Route::post('visitBooth', 'API\SysLogController@visitBooth');
    Route::post('visitVideo', 'API\SysLogController@visitVideo');
    Route::post('visitPoster', 'API\SysLogController@visitPoster');
    Route::post('visitPromotion', 'API\SysLogController@visitPromotion');
    Route::post('visitBrochure', 'API\SysLogController@visitBrochure');
    Route::post('visitInfo', 'API\SysLogController@visitInfo');
    Route::post('visitChat', 'API\SysLogController@visitChat');

    Route::get('getUserActivityLog', 'API\SysLogController@getUserActivityLog');

    Route::get('getLog', 'API\SysLogController@getLog');
    Route::post('getLogExhibitor', 'API\SysLogController@getLogExhibitor');
    Route::get('getVisitorLoginLogCount', 'API\SysLogController@getVisitorLoginLogCount');
    Route::get('getExhibitorLoginLogCount', 'API\SysLogController@getExhibitorLoginLogCount');
    Route::get('getBothLoginLogCount', 'API\SysLogController@getBothLoginLogCount');
    Route::get('getMostVisitBooth', 'API\SysLogController@getMostVisitBooth');
    Route::get('getMostContentView', 'API\SysLogController@getMostContentView');
    Route::get('visitedTrafficLogExport', 'API\SysLogController@visitedTrafficLogExport');

    Route::get('getBackOfficeLoginLog', 'API\SysLogController@getBackOfficeLoginLog');
    Route::get('getLoginCountEachCountry', 'API\SysLogController@getLoginCountEachCountry');

    Route::get('backOfficeLoginLogExport', 'API\SysLogController@backOfficeLoginLogExport');

    Route::get('webinarVisitedListExport', 'API\SysLogController@webinarVisitedListExport');
});
