<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\TemplateSetting;
use App\Models\Certificate;

use Auth;
use Illuminate\Support\Facades\Gate;
use Notification;
use App\Notifications\ApplicationCreateNotification;
use Carbon\Carbon;

/* included models */
use App\Models\Application;
use App\Models\ApplicationService;
use App\Models\ApplicationsProcess;
use App\Models\Countrie;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use App\Models\Union;
use App\Models\Mouza;
use App\Models\Department;
use App\Models\Office;
use App\Models\ApplicationPurpose;
use App\Models\Service;
use App\Models\ServiceItem;
use App\Models\ServiceItemAdditional;
use App\Models\ReceivingMode;
use App\Models\ApplicationsForwardMap;
use App\Models\User;
use App\Models\ServiceCart;
use App\Models\ServiceCartItem;
use App\Models\ApplicationServiceItemDownload;
use App\Models\ApplicationServiceItemDownloadDetail;
use App\Models\AssessmentTemplate;
use App\Models\Payment;
use App\Models\ServiceInventory;
use App\Models\ServiceItemPrice;

// Mails
use App\Mail\ApplicationRejectedMail;
use App\Mail\ApplicationApprovedMail;
use App\Mail\ApplicationForwardMail;
use App\Mail\DownloadTokens;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        menuSubmenu('applications', 'allApplications');
        
        $user = Auth::user();

        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('submitted_applications', $user)) 
            {
                if (Auth::user()->role_id == 10)
                {
                    $applications = Application::where('sr_user_id', Auth::user()->id)->with('receiverRole')->latest()->paginate(15);
                    
                    return view('backend.serviceRecipient.application.index', compact('applications'));
                }
                elseif ((Auth::user()->role_id == 1) || (Auth::user()->role_id == 2))
                {
                    
                    $divisions = Division::where('status', 1)->get();
                    $districts = District::where('status', 1)->get();
                    $upazilas = Upazila::where('status', 1)->get();
                    $unions = Union::where('status', 1)->get();
                    $mouzas = Mouza::where('status', 1)->get();
                    $applications = Application::latest()->paginate(15);

                    return view('backend.serviceRecipient.application.index', compact('applications', 'divisions', 'districts', 'upazilas', 'unions', 'mouzas'));

                } 
                elseif ((Auth::user()->role_id == 11))
                {
                    
                    $divisions = Division::where('status', 1)->get();
                    $districts = District::where('status', 1)->get();
                    $upazilas = Upazila::where('status', 1)->get();
                    $unions = Union::where('status', 1)->get();
                    $mouzas = Mouza::where('status', 1)->get();

                    $applications = ApplicationsProcess::select('application_id')
                                    ->with('application')
                                    // ->where('sender_role_id', Auth::user()->role_id)
                                    ->where('receiver_role_id', Auth::user()->role_id)
                                    ->distinct()->latest()->paginate(15);
                    // dd($applications);
                    return view('backend.serviceRecipient.application.officeAllApplication', compact('applications', 'divisions', 'districts', 'upazilas', 'unions', 'mouzas'));
                }

                else 
                {
                    
                    $divisions = Division::where('status', 1)->get();
                    $districts = District::where('status', 1)->get();
                    $upazilas = Upazila::where('status', 1)->get();
                    $unions = Union::where('status', 1)->get();
                    $mouzas = Mouza::where('status', 1)->get();

                    $applications = ApplicationsProcess::select('application_id')
                                    ->with('application')
                                    ->where('sender_role_id', Auth::user()->role_id)
                                    ->orWhere('receiver_role_id', Auth::user()->role_id)
                                    ->distinct()->latest()->paginate(15);
                    
                    return view('backend.serviceRecipient.application.officeAllApplication', compact('applications', 'divisions', 'districts', 'upazilas', 'unions', 'mouzas'));
                    
                }
                
            }
            else
            {
                abort(403);
            }
        }
        else
        {
            abort(403);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countrys = Countrie::get();
        $divisions = Division::where('status', 1)->get();
        $districts = District::where('status', 1)->get();
        $upazilas = Upazila::where('status', 1)->get();
        $unions = Union::where('status', 1)->get();
        $mouzas = Mouza::where('status', 1)->get();
        $purposes = ApplicationPurpose::where('status', 1)->get();
        $departments = Department::where('status', 1)->get();
        $services = Service::where('id', '!=', 2)->where('status', 1)->get();
        $serviceItems = ServiceItem::where('status', 1)->get();
        $serviceItemAdditionals = ServiceItemAdditional::where('status', 1)->get();
        $receivingModes = ReceivingMode::where('status', 1)->get();
        
        return view('frontend.appCreate', compact('countrys', 'divisions', 'districts', 'upazilas', 'unions', 'mouzas', 'purposes', 'services', 'serviceItems', 'serviceItemAdditionals', 'receivingModes', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function publicationApp()
    {
        $countrys = Countrie::get();
        $divisions = Division::where('status', 1)->get();
        $districts = District::where('status', 1)->get();
        $upazilas = Upazila::where('status', 1)->get();
        $unions = Union::where('status', 1)->get();
        $mouzas = Mouza::where('status', 1)->get();
        $purposes = ApplicationPurpose::where('status', 1)->get();
        $departments = Department::where('status', 1)->get();
        $services = Service::where('status', 1)->get();
        $serviceItems = ServiceItem::where('service_id', 2)->where('status', 1)->get();
        $serviceInventoryItems = ServiceInventory::where('service_id', 2)->where('status', 1)->get();
        $serviceItemAdditionals = ServiceItemAdditional::where('status', 1)->get();
        $receivingModes = ReceivingMode::where('status', 1)->get();

        return view('frontend.publicationApp', compact('countrys', 'divisions', 'districts', 'upazilas', 'unions', 'mouzas', 'purposes', 'services', 'serviceItems', 'serviceItemAdditionals', 'receivingModes', 'departments', 'serviceInventoryItems'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function publicationAppAdmin()
    {
        menuSubmenu('publicationAppAdmin', 'publicationAppAdmin');

        $countrys = Countrie::get();
        $divisions = Division::where('status', 1)->get();
        $districts = District::where('status', 1)->get();
        $upazilas = Upazila::where('status', 1)->get();
        $unions = Union::where('status', 1)->get();
        $mouzas = Mouza::where('status', 1)->get();
        $purposes = ApplicationPurpose::where('status', 1)->get();
        $departments = Department::where('status', 1)->get();
        $services = Service::where('status', 1)->get();
        $serviceItems = ServiceItem::where('service_id', 2)->where('status', 1)->get();
        $serviceInventoryItems = ServiceInventory::where('service_id', 2)->where('status', 1)->get();
        $serviceItemAdditionals = ServiceItemAdditional::where('status', 1)->get();
        $receivingModes = ReceivingMode::where('status', 1)->get();

        return view('backend.serviceRecipient.application.publicationApp', compact('countrys', 'divisions', 'districts', 'upazilas', 'unions', 'mouzas', 'purposes', 'services', 'serviceItems', 'serviceItemAdditionals', 'receivingModes', 'departments', 'serviceInventoryItems'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $check_user_exist = User::where('email', $request->email)->first();

        $serviceItemPrice = ServiceItemPrice::get();

        if(Auth::user())
        {
            if ($request->usage_type == 1) {
                $request->validate([
                    // 'parent_name'               => 'required',
                    'organization_address'      => 'required',
                    'organization_designation'  => 'required',
                    'service_id'                => 'required',
                    'service_item_id'           => 'required',
                    'terms'                     => 'required',
                    'applicant_text'            => 'required',
                    'application_sub'           => 'required',
                ]);
            }
            else if ($request->usage_type == 3) {
                $request->validate([
                    // 'parent_name'               => 'required',
                    // 'personal_occupation'       => 'required',
                    'personal_institute'        => 'required',
                    'service_id'                => 'required',
                    'service_item_id'           => 'required',
                    'terms'                     => 'required',
                    'applicant_text'            => 'required',
                    'application_sub'           => 'required',
                ]);
            } else {
                $request->validate([
                    // 'parent_name'               => 'required',
                    'usage_type'                => 'required',
                    'service_id'                => 'required',
                    'service_item_id'           => 'required',
                    'terms'                     => 'required',
                    'applicant_text'            => 'required',
                    'application_sub'           => 'required',
                ]);
            }

            $application = new Application;

            $application->sr_user_id                = Auth::id();
            $application->office_id                 = $request->office_id;
            $application->division_id               = $request->division_id;
            $application->district_id               = $request->district_id;
            $application->upazila_id                = $request->upazila_id;
            $application->union_id                  = $request->union_id;
            $application->mouza_id                  = $request->mouza_id;
            $application->parent_name               = $request->parent_name;
            $application->usage_type                = $request->usage_type;
            $application->organization_name         = $request->organization_name;
            $application->organization_address      = $request->organization_address;
            $application->organization_designation  = $request->organization_designation;
            $application->personal_occupation       = $request->personal_occupation;
            $application->personal_institute        = $request->personal_institute;
            $application->purpose_id                = $request->purpose_id;
            $application->purpose_specify           = $request->purpose_specify;
            $application->applicant_text            = $request->applicant_text;
            $application->application_sub           = $request->application_sub;
            $application->total_price               = $request->total_price;
            $application->final_total               = $request->total_price;
            $application->receiving_mode_hardcopy   = $request->receiving_mode_hardcopy;
            $application->receiving_mode_softcopy   = $request->receiving_mode_softcopy;
            $application->courier_address           = $request->courier_address;
            $application->terms                     = $request->terms;
            $application->current_sender_role_id    = 10;

            foreach($request->service_id as $serviceId)
            {
                $application->current_receiver_role_id  = $serviceId == "2" ? "11" : "7";
            }

            $application->status                    = 1;
            
            $application->save();
            
            $application->application_id            = date('Ymd').$application->id;

            $application->save();

            $application_process = new ApplicationsProcess;

            $application_process->application_id = $application->id;
            $application_process->user_id = Auth::id();
            $application_process->sender_role_id = 10;

            foreach($request->service_id as $serviceId)
            {
                $application_process->receiver_role_id = $serviceId =="2" ? "11" : "7";
            }
            
            $application_process->comment = "";
            $application_process->status = 1;
            $application_process->receive_time = date("Y-m-d H:i:s");

            $application_process->save();

            $service_ids = $request->service_item_id;
            $service_inventory_items = $request->service_inventory_item_id;
            
            if (in_array('2', $request->service_id)) {

                if (count($service_inventory_items) > 0) {
                    foreach ($service_inventory_items as $service_inventory_item_id) {

                        $serviceItemPrice = ServiceItemPrice::get();

                        $service_inventory_item = ServiceInventory::where('id', $service_inventory_item_id)->first();

                        $application_service = new ApplicationService;
            
                        $application_service->application_id                = $application->id;
                        $application_service->service_id                    = $service_inventory_item->service_id;
                        $application_service->service_item_id               = $service_inventory_item->service_item_id;
                        $application_service->service_inventory_item_id     = $service_inventory_item_id;
                        $application_service->service_inventory_item_price  = $service_inventory_item->price;

                        $application_service->save();
                    }
                }

            } else {

                if (count($request->service_item_id) > 0) {
                    foreach ($service_ids as $service_item_id) {
    
                        $service_item = ServiceItem::where('id', $service_item_id)->first();
                        
                        $application_service = new ApplicationService;

                        $application_service->application_id        = $application->id;
                        $application_service->service_id            = $service_item->service_id;
                        $application_service->service_item_id       = $service_item_id;

                        if ($service_item->service_id == 1) {
                            if ($request->usage_type == 1) {
                                if ($service_item->service_item_type == 1) {
                                    $application_service->service_item_price    = $serviceItemPrice[1]->price;
                                } else {
                                    $application_service->service_item_price    = $serviceItemPrice[0]->price;
                                }
                            } else if ($request->usage_type == 2) {
                                if (Auth::user()->country_id == 19) {
                                    $application_service->service_item_price    = $serviceItemPrice[2]->price;
                                } else {
                                    $application_service->service_item_price    = $serviceItemPrice[3]->price;
                                }
                            } else if ($request->usage_type == 3) {
                                if ($service_item->service_item_type == 1) {
                                    $application_service->service_item_price    = $serviceItemPrice[5]->price;
                                } else {
                                    $application_service->service_item_price    = $serviceItemPrice[4]->price;
                                }
                            }
                        } else if ($service_item->service_id == 3) {
                            if ($request->usage_type == 1) {
                                // if ($request->country_id == 19) {
                                //     $application_service->service_item_price    = $service_item->price_bdt_org;
                                // } else {
                                //     $application_service->service_item_price    = $service_item->price_usd_org;
                                // }
                                $application_service->service_item_price    = $service_item->price_usd_org;
                            } else {
                                // if ($request->country_id == 19) {
                                //     $application_service->service_item_price    = $service_item->price_bdt_personal;
                                // } else {
                                //     $application_service->service_item_price    = $service_item->price_usd_personal;
                                // }
                                $application_service->service_item_price    = $service_item->price_usd_personal;
                            }
                        }
    
                        $application_service->save();
                    }
                    
                }

            } 
        } else {
            
            if ($request->usage_type == 1) {
                
                $request->validate([
                    'first_name'                => ['required', 'regex:/^[A-Za-z0-9 ]+$/'],
                    'last_name'                 => ['required', 'regex:/^[A-Za-z0-9 ]+$/'],
                    // 'parent_name'               => 'required',
                    'email'                     => 'required',
                    'password'                  => 'required|confirmed|min:8',
                    'present_address'           => 'required',
                    'country_id'                => 'required',
                    'organization_address'      => 'required',
                    'organization_designation'  => 'required',
                    'service_id'                => 'required',
                    'service_item_id'           => 'required',
                    'terms'                     => 'required',
                    'applicant_text'            => 'required',
                    'application_sub'           => 'required',
                ]);
            }
            else if ($request->usage_type == 2) {
                $request->validate([
                    'first_name'                => ['required', 'regex:/^[A-Za-z0-9 ]+$/'],
                    'last_name'                 => ['required', 'regex:/^[A-Za-z0-9 ]+$/'],
                    // 'parent_name'               => 'required',
                    'email'                     => 'required',
                    'password'                  => 'required|confirmed|min:8',
                    'present_address'           => 'required',
                    'country_id'                => 'required',
                    // 'personal_occupation'       => 'required',
                    'personal_institute'        => 'required',
                    'service_id'                => 'required',
                    'service_item_id'           => 'required',
                    'terms'                     => 'required',
                    'applicant_text'            => 'required',
                    'application_sub'           => 'required',
                ]);
            } else {
                $request->validate([
                    'first_name'                => ['required', 'regex:/^[A-Za-z0-9 ]+$/'],
                    'last_name'                 => ['required', 'regex:/^[A-Za-z0-9 ]+$/'],
                    // 'parent_name'               => 'required',
                    'email'                     => 'required',
                    'password'                  => 'required|confirmed|min:8',
                    'present_address'           => 'required',
                    'country_id'                => 'required',
                    'usage_type'                => 'required',
                    'service_id'                => 'required',
                    'service_item_id'           => 'required',
                    'terms'                     => 'required',
                    'applicant_text'            => 'required',
                    'application_sub'           => 'required',
                ]);
            }

            if($check_user_exist == '')
            {
                $user = new User;

                $user->role_id         = 10;
                $user->password        = bcrypt($request->password);
                $user->first_name      = $request->first_name;
                $user->last_name       = $request->last_name;
                $user->username        = strtolower($request->first_name.$request->last_name);
                $user->present_address = $request->present_address;
                $user->country_id      = $request->country_id;
                $user->division_id     = $request->division_id;
                $user->district_id     = $request->district_id;
                $user->upazila_id      = $request->upazila_id;
                $user->union_id        = $request->union_id;
                $user->mouza_id        = $request->mouza_id;
                $user->email           = $request->email;
                $user->mobile          = $request->mobile ? $request->mobile : null;

                $user->save();

                $application = new Application;

                $application->sr_user_id                = $user->id;
                $application->office_id                 = $request->office_id;
                $application->division_id               = $request->division_id;
                $application->district_id               = $request->district_id;
                $application->upazila_id                = $request->upazila_id;
                $application->union_id                  = $request->union_id;
                $application->mouza_id                  = $request->mouza_id;
                $application->parent_name               = $request->parent_name;
                $application->usage_type                = $request->usage_type;
                $application->organization_name         = $request->organization_name;
                $application->organization_address      = $request->organization_address;
                $application->organization_designation  = $request->organization_designation;
                $application->personal_occupation       = $request->personal_occupation;
                $application->personal_institute        = $request->personal_institute;
                $application->purpose_id                = $request->purpose_id;
                $application->purpose_specify           = $request->purpose_specify;
                $application->applicant_text            = $request->applicant_text;
                $application->application_sub           = $request->application_sub;
                $application->total_price               = $request->total_price;
                $application->discount                  = 0;
                $application->final_total               = $request->total_price;
                $application->receiving_mode_hardcopy   = $request->receiving_mode_hardcopy;
                $application->receiving_mode_softcopy   = $request->receiving_mode_softcopy;
                $application->courier_address           = $request->courier_address;
                $application->terms                     = $request->terms;
                $application->current_sender_role_id    = 10;

                foreach($request->service_id as $serviceId)
                {
                    $application->current_receiver_role_id  = $serviceId == "2" ? "11" : "7";
                }
                
                $application->status                    = 1;
                
                $application->save();
                
                $application->application_id            = date('Ymd').$application->id;

                $application->save();

                $application_process = new ApplicationsProcess;

                $application_process->application_id = $application->id;
                $application_process->user_id = $user->id;
                $application_process->sender_role_id = 10;

                foreach($request->service_id as $serviceId)
                {
                    $application_process->receiver_role_id = $serviceId =="2" ? "11" : "7";
                }
                
                $application_process->comment = "";
                $application_process->status = 1;
                $application_process->receive_time = date("Y-m-d H:i:s");

                $application_process->save();

                $service_ids = $request->service_item_id;
                $service_inventory_items = $request->service_inventory_item_id;
                
                if (in_array('2', $request->service_id)) {
                    
                    if (count($service_inventory_items) > 0) {
                        foreach ($service_inventory_items as $service_inventory_item_id) {

                            $service_inventory_item = ServiceInventory::where('id', $service_inventory_item_id)->first();

                            $application_service = new ApplicationService;
                
                            $application_service->application_id                = $application->id;
                            $application_service->service_id                    = $service_inventory_item->service_id;
                            $application_service->service_item_id               = $service_inventory_item->service_item_id;
                            $application_service->service_inventory_item_id     = $service_inventory_item_id;
                            $application_service->service_inventory_item_price  = $service_inventory_item->price;

                            $application_service->save();
                        }
                    }

                } else {
                    if (count($request->service_item_id) > 0) {
                        foreach ($service_ids as $service_item_id) {
        
                            $service_item = ServiceItem::where('id', $service_item_id)->first();
                            
                            $application_service = new ApplicationService;
                            
                            $application_service->application_id        = $application->id;
                            $application_service->service_id            = $service_item->service_id;
                            $application_service->service_item_id       = $service_item_id;

                            if ($request->usage_type == 1) {
                                if ($service_item->service_item_type == 1) {
                                    $application_service->service_item_price    = $serviceItemPrice[1]->price;
                                } else {
                                    $application_service->service_item_price    = $serviceItemPrice[0]->price;
                                }
                            } else if ($request->usage_type == 2) {
                                if (Auth::user()->country_id == 19) {
                                    $application_service->service_item_price    = $serviceItemPrice[2]->price;
                                } else {
                                    $application_service->service_item_price    = $serviceItemPrice[3]->price;
                                }
                            }
        
                            $application_service->save();
                        }
                        
                    }
                }

                if($request->ajax())
                {
                    return 1;
                } 

            } else {
                if($request->ajax())
                {
                    return 2;
                } 
            }
        }

        if($request->application_for == 'micro_data'){
            // Send database notification after creating application to assistant officer
            $receiver_user_id = User::where('role_id', 7)->select('id')->get();

            $data = 'New Micro data request submitted';

            if (Auth::user()) {
                $sender_user_id = Auth::id();
            } else {
                $sender_user_id = $user->id;
            }

            $applicationID = $application->id;
            $gotoURL = route('admin.application.show', $applicationID);

            Notification::send($receiver_user_id, new ApplicationCreateNotification($data, $sender_user_id, $gotoURL, $applicationID));

        }else{

            // Send database notification after creating application to Store Manageer
            $receiver_user_id = User::where('role_id', 11)->select('id')->get();

            $data = 'New Publication data request submitted';

            if (Auth::user()) {
                $sender_user_id = Auth::id();
            } else {
                $sender_user_id = $user->id;
            }

            $applicationID = $application->id;
            $gotoURL = route('admin.application.show', $applicationID);

            Notification::send($receiver_user_id, new ApplicationCreateNotification($data, $sender_user_id, $gotoURL, $applicationID));
        }

        
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveCartOnchange(Request $request)
    {
        
        $check_service_cart = ServiceCart::where('sr_user_id', Auth::id())->first();

        $check_service_cart->parent_name               = $request->parent_name;
        $check_service_cart->usage_type                = $request->usage_type;
        $check_service_cart->organization_name         = $request->organization_name;
        $check_service_cart->organization_address      = $request->organization_address;
        $check_service_cart->organization_designation  = $request->organization_designation;
        $check_service_cart->personal_occupation       = $request->personal_occupation;
        $check_service_cart->personal_institute        = $request->personal_institute;
        $check_service_cart->purpose_id                = $request->purpose_id;
        $check_service_cart->purpose_specify           = $request->purpose_specify;
        $check_service_cart->receiving_mode_hardcopy   = $request->receiving_mode_hardcopy;
        $check_service_cart->receiving_mode_softcopy   = $request->receiving_mode_softcopy;
        $check_service_cart->courier_address           = $request->courier_address;
        $check_service_cart->terms                     = $request->terms;

        $check_service_cart->save();

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveCart(Request $request)
    {
        $check_service_cart = ServiceCart::where('sr_user_id', Auth::id())->first();

        $check_service_cart->parent_name               = $request->parent_name;
        $check_service_cart->usage_type                = $request->usage_type;
        $check_service_cart->organization_name         = $request->organization_name;
        $check_service_cart->organization_address      = $request->organization_address;
        $check_service_cart->organization_designation  = $request->organization_designation;
        $check_service_cart->personal_occupation       = $request->personal_occupation;
        $check_service_cart->personal_institute        = $request->personal_institute;
        $check_service_cart->purpose_id                = $request->purpose_id;
        $check_service_cart->purpose_specify           = $request->purpose_specify;
        $check_service_cart->receiving_mode_hardcopy   = $request->receiving_mode_hardcopy;
        $check_service_cart->receiving_mode_softcopy   = $request->receiving_mode_softcopy;
        $check_service_cart->courier_address           = $request->courier_address;
        $check_service_cart->terms                     = $request->terms;

        $check_service_cart->save();
        
        $service_item_ids = $request->service_item_id;
        
        if (count($service_item_ids) > 0) {
            foreach ($service_item_ids as $service_item_id) {
                if($check_service_cart != null)
                {
                    $service_cart_items = ServiceCartItem::where('service_cart_id', $check_service_cart->id)->get();

                    $serviceItem = ServiceItem::where('id', $service_item_id)
                                                ->select('id', 'service_id', 'item_name_en', 'price_bdt_personal', 'price_bdt_org', 'price_usd_personal', 'price_usd_org')
                                                ->first();

                    $check_service_cart->delete();
                    $service_cart_items->each->delete();

                    $service_cart_item = new ServiceCartItem;
                
                    $service_cart_item->service_cart_id       = $check_service_cart->id;
                    $service_cart_item->service_id            = $serviceItem->service_id;
                    $service_cart_item->service_item_id       = $service_item_id;

                    if ($request->usage_type == 1) {
                        if (Auth::user()->country_id == 19) {
                            $service_cart_item->service_item_price    = $serviceItem->price_bdt_org;
                        } else {
                            $service_cart_item->service_item_price    = $serviceItem->price_usd_org;
                        }
                    } else if ($request->usage_type == 2) {
                        if (Auth::user()->country_id == 19) {
                            $service_cart_item->service_item_price    = $serviceItem->price_bdt_personal;
                        } else {
                            $service_cart_item->service_item_price    = $serviceItem->price_usd_personal;
                        }
                    }

                    $service_cart_item->save();
                }
            }
            
        }

    }

    public function clearCart(ServiceCart $service_cart)
    {
        // dd($service_cart);
        if($service_cart != null)
        {

            $service_cart_items = ServiceCartItem::where('service_cart_id', $service_cart->id)->get();
            
            $service_cart_items->each->delete();
            $service_cart->delete();

            return redirect()->route('admin.application.create');
        } else {
            return redirect()->route('admin.application.create')->with('error', 'Cart is Empty');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    //    dd(1);
        $user = Auth::user();

        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('application_detail', $user)) 
            {
                $application = Application::with('user', 'office', 'division', 'district', 'upazila', 'applicationPurpose', 'receivingModeSoftcopy', 'receivingModeHardcopy', 'senderRole', 'receiverRole', 'applicationStatus')->where('id', $id)->first();

                $applicationCurrentPosition = ApplicationsProcess::with('user', 'senderRole', 'receiverRole', 'designation', 'status')->where('application_id', $id)->orderBy('id', 'DESC')->first();
                
                    
                $applicationServices = ApplicationService::with('service', 'serviceItem')->where('application_id', $id)->get();
                
                $forwards = ApplicationsForwardMap::with('senderRole', 'forwardRole')
                                                    ->where('sender_role_id', Auth::user()->role_id)
                                                    ->where('forward_role_id', '<', Auth::user()->role_id)
                                                    ->get();
                $backwards = ApplicationsForwardMap::with('senderRole', 'forwardRole')
                                                    ->where('sender_role_id', Auth::user()->role_id)
                                                    ->where('forward_role_id', '>', Auth::user()->role_id)
                                                    ->get();

                $applicationProcess = ApplicationsProcess::with('user', 'senderRole', 'receiverRole', 'designation', 'status')->where('application_id', $id)->get();
                
                $isApprovedPerson = ApplicationsForwardMap::where('forward_role_id', Auth::user()->role_id)
                                                          ->where('is_approved_person', 1)
                                                          ->first();
                
                
                return view('backend.serviceRecipient.application.show', compact('application', 'applicationServices', 'forwards', 'backwards', 'applicationProcess', 'id', 'isApprovedPerson', 'applicationCurrentPosition'));
            }
            else
            {
                abort(403);
            }
        }
        else
        {
            abort(403);
        }
    }

    /**
     * Application nothi assessment method
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assessment(Application $application)
    {
        
        $assessmentTemplate = AssessmentTemplate::where('status', 1)->first();
        $applicationProcess = ApplicationsProcess::where('application_id', $application->id)->get();

        return view('backend.eDocuments.assessment', compact('applicationProcess', 'application', 'assessmentTemplate'));
    }

    /**
     * Application Forwarding method
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forward($id, Request $request)
    {
        
        $user = Auth::user();

        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('forward_application', $user)) 
            {

                $application = Application::where('id', $id)->first();

                $lastAppProcess = ApplicationsProcess::where('application_id', $application->id)->orderBy('id', 'DESC')->first();

                $applicationProcess = new ApplicationsProcess;
                $applicationProcess->application_id = $application->id;
                $applicationProcess->status = 3;
                $applicationProcess->user_id = Auth::user()->id;
                $applicationProcess->sender_role_id = Auth::user()->role_id;
                $applicationProcess->receiver_role_id = $request->forward_role_id;
                $applicationProcess->sender_designation_id = Auth::user()->designation_id;
                $applicationProcess->sender_signature = Auth::user()->signature;
                $applicationProcess->comment = $request->comment;
                $applicationProcess->receive_time = $lastAppProcess->created_at;
                $processDone = $applicationProcess->save();

                $application->current_sender_role_id = Auth::user()->role_id;
                $application->current_receiver_role_id = $request->forward_role_id;
                $application->status = 3;

                if($request->hasFile('file'))
                {
                    $cp = $request->file('file');
                    $extension = strtolower($cp->getClientOriginalExtension());
                    $randomFileName = $applicationProcess->id.'file'.date('Y_m_d_his').'_'.rand(10000000,99999999).'.'.$extension;

                    #delete old rows of profilepic
                    Storage::disk('public')->put('attachments/'.$randomFileName, File::get($cp));

                    
                    $applicationProcess->attachment = $randomFileName;
                    $applicationProcess->save();
                } 
                $appDone = $application->save();

                // mail data
                $user_data = User::where('role_id', $request->forward_role_id)->get();

                foreach ($user_data as $ud)
                {
                    // send database notification to dashboard
                    $receiver_user_id = User::where('role_id', $request->forward_role_id)->select('id')->get();
                    $data = 'You have received an application';
                    $sender_user_id = Auth::user()->id;
                    $applicationID = $application->id;
                    $gotoURL = route('admin.application.show', $applicationID);
                    Notification::send($receiver_user_id, new ApplicationCreateNotification($data, $sender_user_id, $gotoURL, $applicationID));

                    // Send email notification
                    Mail::to($ud->email)->send(new ApplicationForwardMail($ud));
                }

                if($processDone && $appDone)
                {
                    return redirect()->route('admin.application.index')->with('success', 'Application forward successfully...!');
                }else{
                    return redirect()->route('admin.application.index')->with('error', 'Something went wrong, Please try again...!');
                }
            }
            else
            {
                abort(403);
            }
        }
        else
        {
            abort(403);
        }

    }

    /**
     * Cancel the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        
        $user = Auth::user();

        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('cancel_application', $user)) 
            {
                $application = Application::where('id', $id)->first();

                $applicationProcess = new ApplicationsProcess;
                $applicationProcess->application_id = $application->id;
                $applicationProcess->status = 5;
                $applicationProcess->user_id = Auth::user()->id;
                $applicationProcess->sender_role_id = Auth::user()->role_id;
                $applicationProcess->sender_designation_id = Auth::user()->designation_id;
                $applicationProcess->sender_signature = Auth::user()->signature;
                $applicationProcess->comment = $request->comment;
                $processDone = $applicationProcess->save();

                $application->current_sender_role_id = Auth::user()->role_id;
                $application->is_approved = 0;
                $application->status = 5;

                $appDone = $application->save();

                // Prepare mail data
                $sr_user_id = $application->sr_user_id;
                $sr_user = User::where('id', $sr_user_id)->first();
                $reason = $request->comment;

                if ($processDone && $appDone)
                {
                    // send database notification to dashboard
                    $receiver_user_id = User::where('id', $application->sr_user_id)->select('id')->get();
                    $data = 'One of your application is Rejected';
                    $sender_user_id = Auth::user()->id;
                    $applicationID = $application->id;
                    $gotoURL = route('admin.application.show', $applicationID);
                    Notification::send($receiver_user_id, new ApplicationCreateNotification($data, $sender_user_id, $gotoURL, $applicationID));

                    Mail::to($sr_user->email)->send(new ApplicationRejectedMail($sr_user, $reason));

                    return redirect()->route('admin.application.index')->with('success', 'Application Rejected successfully...!');
                } else {
                    return redirect()->route('admin.application.index')->with('error', 'Something went wrong, Please try again...!');
                }
            }
            else
            {
                abort(403);
            }
            
        }
        else
        {
            abort(403);
        }
    }

    /**
     * Approve the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        
        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('approve_application', $user)) 
            {
                $application = Application::with('allApplicationServices')->where('id', $id)->first();

                $applicationProcess = new ApplicationsProcess;
                $applicationProcess->application_id = $application->id;
                $applicationProcess->status = 4;
                $applicationProcess->user_id = Auth::user()->id;
                $applicationProcess->sender_role_id = Auth::user()->role_id;
                $applicationProcess->sender_designation_id = Auth::user()->designation_id;
                $applicationProcess->sender_signature = Auth::user()->signature;
                $applicationProcess->comment = "Application Approved";
                $processDone = $applicationProcess->save();

                $application->current_sender_role_id = Auth::user()->role_id;
                $application->is_approved = 1;
                $application->status = 4;
                $appDone = $application->save();

                // Prepare mail data
                $sr_user_id = $application->sr_user_id;
                $sr_user = User::where('id', $sr_user_id)->first();

                if ($processDone && $appDone)
                {
                    // send database notification to dashboard
                    $receiver_user_id = User::where('id', $application->sr_user_id)->select('id')->get();
                    $data = 'One of your application is Approved';
                    $sender_user_id = Auth::user()->id;
                    $applicationID = $application->id;
                    $gotoURL = route('admin.application.show', $applicationID);
                    Notification::send($receiver_user_id, new ApplicationCreateNotification($data, $sender_user_id, $gotoURL, $applicationID));

                    // Email notification
                    Mail::to($sr_user->email)->send(new ApplicationApprovedMail($sr_user, $application));
                    return redirect()->route('admin.application.index')->with('success', 'Application Approved successfully...!');
                } else {
                    return redirect()->route('admin.application.index')->with('error', 'Something went wrong, Please try again...!');
                }
            }
            else
            {
                abort(403);
            }
        }
        else
        {
            abort(403);
        }
    }



    /**
     * Display a listing of the pending applications.
     *
     * @return \Illuminate\Http\Response
     */
    public function pending()
    {
        menuSubmenu('applications', 'pendingApplications');

        $user = Auth::user();

        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('pending_applications', $user)) 
            {
                if (Auth::user()->role_id == 10)
                {
                    $userID = Auth::user()->id;
                    $applications = Application::where('sr_user_id', $userID)->where('status', 1)->latest()->paginate(25);
                    return view('backend.serviceRecipient.application.pending', compact('applications'));

                } 
                elseif ((Auth::user()->role_id == 1) || (Auth::user()->role_id == 2)) 
                {

                    $applications = Application::where('status', 1)->latest()->paginate(15);
                    return view('backend.serviceRecipient.application.pending', compact('applications'));

                } else {
                    $applications = Application::where('status', 1)->where('current_receiver_role_id', Auth::user()->role_id)
                                                ->latest()->paginate(15);
                    return view('backend.serviceRecipient.application.pending', compact('applications'));
                }
                
            }
            else
            {
                abort(403);
            }
        }
        else
        {
            abort(403);
        }
    }

    /**
     * Display a listing of the processing applications.
     *
     * @return \Illuminate\Http\Response
     */
    public function processing()
    {
        menuSubmenu('applications', 'processingApplications');

        $user = Auth::user();

        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('processing_applications', $user)) 
            {
                if (Auth::user()->role_id == 10)
                {
                    $userID = Auth::user()->id;
                    $applications = Application::where([
                                                        ['sr_user_id', $userID],
                                                        ['status', 2]
                                                    ])
                                                ->orWhere([
                                                        ['sr_user_id', $userID],
                                                        ['status', 3]
                                                    ])
                                                ->latest()->paginate(15);
                        
                    return view('backend.serviceRecipient.application.processing', compact('applications'));

                } 
                elseif ((Auth::user()->role_id == 1) || (Auth::user()->role_id == 2)) 
                {

                    $applications = Application::where('status', 2)->orWhere('status', 3)->latest()->paginate(15);
                    return view('backend.serviceRecipient.application.processing', compact('applications'));

                } else {
                    $application_ids = ApplicationsProcess::select('application_id')->distinct()->get();
                    
                    $applications = Application::whereIn('id', $application_ids)
                                    ->where('status', 2)
                                    ->orWhere('status', 3)
                                    ->latest()->paginate(15);
                    
                    return view('backend.serviceRecipient.application.officeProcessingApplication', compact('applications'));
                }
                
            }
            else
            {
                abort(403);
            }
        }
        else
        {
            abort(403);
        }
    }


    /**
     * Display a listing of the approved applications.
     *
     * @return \Illuminate\Http\Response
     */
    public function approved()
    {
        
        menuSubmenu('applications', 'approvedApplications');

        $user = Auth::user();

        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('approved_applications', $user)) 
            {
                if (Auth::user()->role_id == 10)
                {
                    $userID = Auth::user()->id;
                    $applications = Application::where('sr_user_id', $userID)->where('status', 4)->latest()->paginate(25);
                    return view('backend.serviceRecipient.application.approved', compact('applications'));

                } 
                elseif ((Auth::user()->role_id == 1) || (Auth::user()->role_id == 2)) 
                {

                    $applications = Application::where('status', 4)->latest()->paginate(25);
                    return view('backend.serviceRecipient.application.approved', compact('applications'));

                } else {
                    $application_ids = ApplicationsProcess::select('application_id')->distinct()->get();
                    
                    $applications = Application::whereIn('id', $application_ids)
                                    ->where('status', 4)
                                    ->latest()->paginate(15);

                    return view('backend.serviceRecipient.application.officeApprovedApplication', compact('applications'));
                }
                
            }
            else
            {
                abort(403);
            }
        }
        else
        {
            abort(403);
        }
    }

    /**
     * Display a listing of the canceled applications.
     *
     * @return \Illuminate\Http\Response
     */
    public function canceled()
    {
        menuSubmenu('applications', 'canceledApplications');

        $user = Auth::user();

        if (Gate::allows('manage_applications', $user)) 
        {
            if (Gate::allows('canceled_applications', $user)) 
            {
                if (Auth::user()->role_id == 10)
                {
                    $userID = Auth::user()->id;
                    $applications = Application::where('sr_user_id', $userID)->where('status', 5)->latest()->paginate(15);
                    return view('backend.serviceRecipient.application.canceled', compact('applications'));

                } 
                elseif ((Auth::user()->role_id == 1) || (Auth::user()->role_id == 2)) 
                {

                    $applications = Application::where('status', 5)->latest()->paginate(15);
                    return view('backend.serviceRecipient.application.canceled', compact('applications'));

                } else {
                    $application_ids = ApplicationsProcess::select('application_id')->distinct()->get();
                    
                    $applications = Application::whereIn('id', $application_ids)
                                    ->where('status', 5)
                                    ->latest()->paginate(15);

                    return view('backend.serviceRecipient.application.officeRejectedApplication', compact('applications'));
                }
                
            }
            else
            {
                abort(403);
            }
        }
        else
        {
            abort(403);
        }
    }

    public function manualPay(Application $application)
    {
        return view('backend.serviceRecipient.payment.manualPay',[
            'application' => $application
        ]);

    }

    // Payment Gateway
    public function responseEkpaySuccess(Request $request)
    {
        
        // sleep(15);
        $Payment = Payment::where('id',$request->transId)->first();
        
        if($_SERVER['SERVER_NAME']=='127.0.0.1' OR $_SERVER['SERVER_NAME']=='localhost' OR $_SERVER['SERVER_NAME']=='bbs.oo' OR $_SERVER['SERVER_NAME'] == 'bbs.sebpobd.com'){   
            $Payment->transaction_id = $request->transId;
            $Payment->save();
        }

        if(!empty($Payment->transaction_id)){
            $pay = Payment::where('id',$request->transId)->first();
            $pay->is_app = true;
            $application = Application::where('id',$pay->application_id)->first();
            $application->is_paid = true;
            $application->save();

            $applicationServices = ApplicationService::with('serviceItem')->where('application_id', $pay->application_id)->get();

            foreach($applicationServices as $item)
            {
                // generate unique link
                $currentDatetime = date('Ymdhis');
                $str = rand();
                $mdstr = md5($str);
                $link = $currentDatetime . $mdstr;

                // Generate unique download token for service items
                $str2 = rand();
                $mdstr2 = md5($str2);
                $downloadToken = $currentDatetime . $mdstr2;

                $appServiceItemDownload = new ApplicationServiceItemDownload;
                $appServiceItemDownload->application_id = $item->application_id;
                $appServiceItemDownload->service_id = $item->service_id;
                $appServiceItemDownload->service_inventory_item_id = $item->service_inventory_item_id; // for service inventory item


                $appServiceItemDownload->service_item_id = $item->serviceItem->id;
                $appServiceItemDownload->file_path = "storage/service/item/". $item->serviceItem->attachment;
                $appServiceItemDownload->link = $link;
                $appServiceItemDownload->download_token = $downloadToken;
                $appServiceItemDownload->total_download = 0;
                $appServiceItemDownload->save();

                if($appServiceItemDownload->service_id == 3)
                {
                    $template = TemplateSetting::where('service_item_id',$appServiceItemDownload->service_item_id)->first();
                    // dd($template);
                    if($template)
                    {
                        $certificate = new Certificate;
                    
                        $certificate->application_id = $appServiceItemDownload->application_id;
                        $certificate->sr_user_id = $appServiceItemDownload->application->sr_user_id;
                        $certificate->service_item_id = $appServiceItemDownload->service_item_id;
                        // $certificate->certificate_no = 
                        // $certificate->certificate_date = 
                        $certificate->content = $template->body;
                        $certificate->template_id = $template->id;
                        // $certificate->office_id = 
                        // $certificate->level_id = 
                        // $certificate->division_id = $appServiceItemDownload->application->division_id;
                        // $certificate->district_id = $appServiceItemDownload->application->district_id;
                        // $certificate->upazila_id = $appServiceItemDownload->application->upazila_id;
                        $certificate->created_by = Auth::id();
                        $certificate->created_by_signature = Auth::user()->signature;
                        $certificate->created_by_designation = Auth::user()->designation_id; 
                        // $certificate->modified = 
                        $certificate->status = true;
                        $certificate->save();
                    }
                    else
                    {
                        return back()->with('info','No Template Found for this certificate service.');
                    }
                }

            }

            $pay->save();

            // Email Datas
            $user = $application->user;
            $downloadTokens = ApplicationServiceItemDownload::where('application_id', $application->id)->get();

            // Email with service items download tokens
            Mail::to($user->email)->send(new DownloadTokens($user, $downloadTokens, $application));


            return redirect('bbs/application/approved')->with('success','Your transaction successful.');
        }else{
            return redirect('bbs/application/approved')->with('warning','Your transaction not successful.');
        }
        
    }


    public function responseEkpayCancel(Request $request)
    {
        return redirect('bbs/application/approved')->with('warning','Your transaction not successful.');
    }

    public function ePay(Application $application)
    {

        $application = Application::where('id',$application->id)->first();

        if(empty($application)){
            return redirect('bbs/application/approved')->with('warning','Your application ID not found.');
        }
        
        $request = new Payment;
        
        $request->division_id = $application->division_id;
        $request->district_id = $application->district_id;
        $request->upazila_id = $application->upazila_id;
        $request->office_id = $application->office_id;
        $request->application_id = $application->id;
        $request->sr_user_id = $application->sr_user_id;
        $request->nid = $application->user ? $application->user->nid_no : '';
        $request->dob = $application->user ? $application->user->dob : '';
        $request->amount = $application->total_price;
        $request->fees = 0;
        $request->transaction_id = null;
        $request->pg_id = 1;
        $request->is_app = 0;
        $request->challan_no = null;
        $request->request_time = date('Y-m-d');
        
        $request->save();

        $reqst_id=$request->id;
        $amount = $application->total_price;

        $date = date('Y-m-d H:i:s');
        $BackUrl=url('bbs');
        $paymentUrl='https://sandbox.ekpay.gov.bd/ekpaypg/';
        $userName='bbs_test';
        $password='BbstaT@tsT12';
        $mac_addr='1.1.1.1';

        $responseUrlSuccess = $BackUrl.'/application/response-ekpay-success';
        $ipnUrlTrxinfo = $BackUrl.'/response-ekpay-ipn-tax';
        $responseUrlCancel = $BackUrl.'/application/response-ekpay-cancel';

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $paymentUrl.'v1/merchant-api',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "mer_info":
            {
                "mer_reg_id":"'.$userName.'",
                "mer_pas_key":"'.$password.'"
            },
            "req_timestamp":"'.$date.' GMT+6",
            "feed_uri":
                {
                    "s_uri":"'.$responseUrlSuccess.'",
                    "f_uri":"'.$responseUrlCancel.'",
                    "c_uri":"'.$responseUrlCancel.'"
                },
                "cust_info":
                {
                    "cust_id":"'.$application->user->id.'",
                    "cust_name":"'.$application->user->first_name.'",
                    "cust_mobo_no":"+88'.$application->user->mobile.'",
                    "cust_email":"'.$application->user->email.'",
                    "cust_mail_addr":"'.$application->user->present_address.'"

                },
                "trns_info":
                {
                    "trnx_id":"'.$reqst_id.'",
                    "trnx_amt":"'.$amount.'",
                    "trnx_currency":"BDT",
                    "ord_id":"'.$reqst_id.'",
                    "ord_det":"BBS"

                },
                "ipn_info":
                {
                    "ipn_channel":"3",
                    "ipn_email":"mafizur.mysoftheaven@gmail.com",
                    "ipn_uri":"'.$ipnUrlTrxinfo.'"

                },
                "mac_addr":"'.$mac_addr.'"

        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $info = json_decode($response);

        $token = $info->secure_token;
        
        if(!empty($token)){
            $redirect= $paymentUrl."v1?sToken=$token&trnsID=$reqst_id";
            return redirect($redirect);
        }else{
            return redirect('bbs/application/approved')->with('warning',$info->msg_det);
        }

    }
    // End Payment Gateway

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function discount(Request $request, Application $application)
    {
        $request->validate([
            'discount' => 'required',
        ]);

        $application->discount    = $request->discount;
        $application->final_total = $request->final_total;
        
        $application->save();

        return redirect()->back();
    }

    public function downloadChalan(Application $application)
    {
        if($application->usage_type == 1)
        {
            $type = $application->organization_designation;
        } 
        elseif($application->usage_type == 2)
        {
            $type =  $application->personal_occupation;
        }
        else
        {
            $type = '';
        }

        $p = '';
        $price = '';
        $total_price = 0;
        $pItem ='';

        $appServices = ApplicationService::where('application_id',$application->id)->get();
        
        foreach ($appServices as $appService) {
            
            $p .= ("<p>".$appService->service->name_bn."</p>");
            $price .= ("<p>".$appService->service_item_price."</p>");
            $total_price += $appService->service_item_price;
            foreach($appService->serviceItems as $item){
                
                $pItem .= ("<p>".$item->item_name_bn."</p>");
            }
        }

        $div = '<div class="container" style="margin-top: 100px;">
                    <style>
                        
                        html {
                            font-family: bangla;
                        -webkit-text-size-adjust: 100%;
                        -ms-text-size-adjust: 100%;
                        }
                        body {
                        margin: 0;
                        }
                        article,
                        aside,
                        details,
                        figcaption,
                        figure,
                        footer,
                        header,
                        hgroup,
                        main,
                        menu,
                        nav,
                        section,
                        summary {
                        display: block;
                        }
                        audio,
                        canvas,
                        progress,
                        video {
                        display: inline-block;
                        vertical-align: baseline;
                        }
                        audio:not([controls]) {
                        display: none;
                        height: 0;
                        }
                        [hidden],
                        template {
                        display: none;
                        }
                        a {
                        background-color: transparent;
                        }
                        a:active,
                        a:hover {
                        outline: 0;
                        }
                        abbr[title] {
                        border-bottom: 1px dotted;
                        }
                        b,
                        strong {
                        font-weight: 700;
                        }
                        dfn {
                        font-style: italic;
                        }
                        h1 {
                        margin: 0.67em 0;
                        font-size: 2em;
                        }
                        mark {
                        color: #000;
                        background: #ff0;
                        }
                        small {
                        font-size: 80%;
                        }
                        sub,
                        sup {
                        position: relative;
                        font-size: 75%;
                        line-height: 0;
                        vertical-align: baseline;
                        }
                        sup {
                        top: -0.5em;
                        }
                        sub {
                        bottom: -0.25em;
                        }
                        img {
                        border: 0;
                        }
                        svg:not(:root) {
                        overflow: hidden;
                        }
                        figure {
                        margin: 1em 40px;
                        }
                        hr {
                        height: 0;
                        -webkit-box-sizing: content-box;
                        -moz-box-sizing: content-box;
                        box-sizing: content-box;
                        }
                        pre {
                        overflow: auto;
                        }
                        code,
                        kbd,
                        pre,
                        samp {
                        font-family: monospace, monospace;
                        font-size: 1em;
                        }
                        button,
                        input,
                        optgroup,
                        select,
                        textarea {
                        margin: 0;
                        font: inherit;
                        color: inherit;
                        }
                        button {
                        overflow: visible;
                        }
                        button,
                        select {
                        text-transform: none;
                        }
                        button,
                        html input[type="button"],
                        input[type="reset"],
                        input[type="submit"] {
                        -webkit-appearance: button;
                        cursor: pointer;
                        }
                        button[disabled],
                        html input[disabled] {
                        cursor: default;
                        }
                        button::-moz-focus-inner,
                        input::-moz-focus-inner {
                        padding: 0;
                        border: 0;
                        }
                        input {
                        line-height: normal;
                        }
                        input[type="checkbox"],
                        input[type="radio"] {
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                        padding: 0;
                        }
                        input[type="number"]::-webkit-inner-spin-button,
                        input[type="number"]::-webkit-outer-spin-button {
                        height: auto;
                        }
                        input[type="search"] {
                        -webkit-box-sizing: content-box;
                        -moz-box-sizing: content-box;
                        box-sizing: content-box;
                        -webkit-appearance: textfield;
                        }
                        input[type="search"]::-webkit-search-cancel-button,
                        input[type="search"]::-webkit-search-decoration {
                        -webkit-appearance: none;
                        }
                        fieldset {
                        padding: 0.35em 0.625em 0.75em;
                        margin: 0 2px;
                        border: 1px solid silver;
                        }
                        legend {
                        padding: 0;
                        border: 0;
                        }
                        textarea {
                        overflow: auto;
                        }
                        optgroup {
                        font-weight: 700;
                        }
                        table {
                        border-spacing: 0;
                        border-collapse: collapse;
                        }
                        td,
                        th {
                        padding: 0;
                        } /*! Source: https://github.com/h5bp/html5-boilerplate/blob/master/src/css/main.css */
                        @media print {
                        *,
                        :after,
                        :before {
                            color: #000 !important;
                            text-shadow: none !important;
                            background: 0 0 !important;
                            -webkit-box-shadow: none !important;
                            box-shadow: none !important;
                        }
                        a,
                        a:visited {
                            text-decoration: underline;
                        }
                        a[href]:after {
                            content: " (" attr(href) ")";
                        }
                        abbr[title]:after {
                            content: " (" attr(title) ")";
                        }
                        a[href^="#"]:after,
                        a[href^="javascript:"]:after {
                            content: "";
                        }
                        blockquote,
                        pre {
                            border: 1px solid #999;
                            page-break-inside: avoid;
                        }
                        thead {
                            display: table-header-group;
                        }
                        img,
                        tr {
                            page-break-inside: avoid;
                        }
                        img {
                            max-width: 100% !important;
                        }
                        h2,
                        h3,
                        p {
                            orphans: 3;
                            widows: 3;
                        }
                        h2,
                        h3 {
                            page-break-after: avoid;
                        }
                        .navbar {
                            display: none;
                        }
                        .btn > .caret,
                        .dropup > .btn > .caret {
                            border-top-color: #000 !important;
                        }
                        .label {
                            border: 1px solid #000;
                        }
                        .table {
                            border-collapse: collapse !important;
                        }
                        .table td,
                        .table th {
                            background-color: #fff !important;
                        }
                        .table-bordered td,
                        .table-bordered th {
                            border: 1px solid #ddd !important;
                        }
                        }
                        @font-face {
                        font-family: "Glyphicons Halflings";
                        src: url(../fonts/glyphicons-halflings-regular.eot);
                        src: url(../fonts/glyphicons-halflings-regular.eot?#iefix)
                            format("embedded-opentype"),
                            url(../fonts/glyphicons-halflings-regular.woff2) format("woff2"),
                            url(../fonts/glyphicons-halflings-regular.woff) format("woff"),
                            url(../fonts/glyphicons-halflings-regular.ttf) format("truetype"),
                            url(../fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular)
                            format("svg");
                        }
                        .glyphicon {
                        position: relative;
                        top: 1px;
                        display: inline-block;
                        font-family: "Glyphicons Halflings";
                        font-style: normal;
                        font-weight: 400;
                        line-height: 1;
                        -webkit-font-smoothing: antialiased;
                        -moz-osx-font-smoothing: grayscale;
                        }
                        .glyphicon-asterisk:before {
                        content: "\002a";
                        }
                        .glyphicon-plus:before {
                        content: "\002b";
                        }
                        .glyphicon-eur:before,
                        .glyphicon-euro:before {
                        content: "\20ac";
                        }
                        .glyphicon-minus:before {
                        content: "\2212";
                        }
                        .glyphicon-cloud:before {
                        content: "\2601";
                        }
                        .glyphicon-envelope:before {
                        content: "\2709";
                        }
                        .glyphicon-pencil:before {
                        content: "\270f";
                        }
                        .glyphicon-glass:before {
                        content: "\e001";
                        }
                        .glyphicon-music:before {
                        content: "\e002";
                        }
                        .glyphicon-search:before {
                        content: "\e003";
                        }
                        .glyphicon-heart:before {
                        content: "\e005";
                        }
                        .glyphicon-star:before {
                        content: "\e006";
                        }
                        .glyphicon-star-empty:before {
                        content: "\e007";
                        }
                        .glyphicon-user:before {
                        content: "\e008";
                        }
                        .glyphicon-film:before {
                        content: "\e009";
                        }
                        .glyphicon-th-large:before {
                        content: "\e010";
                        }
                        .glyphicon-th:before {
                        content: "\e011";
                        }
                        .glyphicon-th-list:before {
                        content: "\e012";
                        }
                        .glyphicon-ok:before {
                        content: "\e013";
                        }
                        .glyphicon-remove:before {
                        content: "\e014";
                        }
                        .glyphicon-zoom-in:before {
                        content: "\e015";
                        }
                        .glyphicon-zoom-out:before {
                        content: "\e016";
                        }
                        .glyphicon-off:before {
                        content: "\e017";
                        }
                        .glyphicon-signal:before {
                        content: "\e018";
                        }
                        .glyphicon-cog:before {
                        content: "\e019";
                        }
                        .glyphicon-trash:before {
                        content: "\e020";
                        }
                        .glyphicon-home:before {
                        content: "\e021";
                        }
                        .glyphicon-file:before {
                        content: "\e022";
                        }
                        .glyphicon-time:before {
                        content: "\e023";
                        }
                        .glyphicon-road:before {
                        content: "\e024";
                        }
                        .glyphicon-download-alt:before {
                        content: "\e025";
                        }
                        .glyphicon-download:before {
                        content: "\e026";
                        }
                        .glyphicon-upload:before {
                        content: "\e027";
                        }
                        .glyphicon-inbox:before {
                        content: "\e028";
                        }
                        .glyphicon-play-circle:before {
                        content: "\e029";
                        }
                        .glyphicon-repeat:before {
                        content: "\e030";
                        }
                        .glyphicon-refresh:before {
                        content: "\e031";
                        }
                        .glyphicon-list-alt:before {
                        content: "\e032";
                        }
                        .glyphicon-lock:before {
                        content: "\e033";
                        }
                        .glyphicon-flag:before {
                        content: "\e034";
                        }
                        .glyphicon-headphones:before {
                        content: "\e035";
                        }
                        .glyphicon-volume-off:before {
                        content: "\e036";
                        }
                        .glyphicon-volume-down:before {
                        content: "\e037";
                        }
                        .glyphicon-volume-up:before {
                        content: "\e038";
                        }
                        .glyphicon-qrcode:before {
                        content: "\e039";
                        }
                        .glyphicon-barcode:before {
                        content: "\e040";
                        }
                        .glyphicon-tag:before {
                        content: "\e041";
                        }
                        .glyphicon-tags:before {
                        content: "\e042";
                        }
                        .glyphicon-book:before {
                        content: "\e043";
                        }
                        .glyphicon-bookmark:before {
                        content: "\e044";
                        }
                        .glyphicon-print:before {
                        content: "\e045";
                        }
                        .glyphicon-camera:before {
                        content: "\e046";
                        }
                        .glyphicon-font:before {
                        content: "\e047";
                        }
                        .glyphicon-bold:before {
                        content: "\e048";
                        }
                        .glyphicon-italic:before {
                        content: "\e049";
                        }
                        .glyphicon-text-height:before {
                        content: "\e050";
                        }
                        .glyphicon-text-width:before {
                        content: "\e051";
                        }
                        .glyphicon-align-left:before {
                        content: "\e052";
                        }
                        .glyphicon-align-center:before {
                        content: "\e053";
                        }
                        .glyphicon-align-right:before {
                        content: "\e054";
                        }
                        .glyphicon-align-justify:before {
                        content: "\e055";
                        }
                        .glyphicon-list:before {
                        content: "\e056";
                        }
                        .glyphicon-indent-left:before {
                        content: "\e057";
                        }
                        .glyphicon-indent-right:before {
                        content: "\e058";
                        }
                        .glyphicon-facetime-video:before {
                        content: "\e059";
                        }
                        .glyphicon-picture:before {
                        content: "\e060";
                        }
                        .glyphicon-map-marker:before {
                        content: "\e062";
                        }
                        .glyphicon-adjust:before {
                        content: "\e063";
                        }
                        .glyphicon-tint:before {
                        content: "\e064";
                        }
                        .glyphicon-edit:before {
                        content: "\e065";
                        }
                        .glyphicon-share:before {
                        content: "\e066";
                        }
                        .glyphicon-check:before {
                        content: "\e067";
                        }
                        .glyphicon-move:before {
                        content: "\e068";
                        }
                        .glyphicon-step-backward:before {
                        content: "\e069";
                        }
                        .glyphicon-fast-backward:before {
                        content: "\e070";
                        }
                        .glyphicon-backward:before {
                        content: "\e071";
                        }
                        .glyphicon-play:before {
                        content: "\e072";
                        }
                        .glyphicon-pause:before {
                        content: "\e073";
                        }
                        .glyphicon-stop:before {
                        content: "\e074";
                        }
                        .glyphicon-forward:before {
                        content: "\e075";
                        }
                        .glyphicon-fast-forward:before {
                        content: "\e076";
                        }
                        .glyphicon-step-forward:before {
                        content: "\e077";
                        }
                        .glyphicon-eject:before {
                        content: "\e078";
                        }
                        .glyphicon-chevron-left:before {
                        content: "\e079";
                        }
                        .glyphicon-chevron-right:before {
                        content: "\e080";
                        }
                        .glyphicon-plus-sign:before {
                        content: "\e081";
                        }
                        .glyphicon-minus-sign:before {
                        content: "\e082";
                        }
                        .glyphicon-remove-sign:before {
                        content: "\e083";
                        }
                        .glyphicon-ok-sign:before {
                        content: "\e084";
                        }
                        .glyphicon-question-sign:before {
                        content: "\e085";
                        }
                        .glyphicon-info-sign:before {
                        content: "\e086";
                        }
                        .glyphicon-screenshot:before {
                        content: "\e087";
                        }
                        .glyphicon-remove-circle:before {
                        content: "\e088";
                        }
                        .glyphicon-ok-circle:before {
                        content: "\e089";
                        }
                        .glyphicon-ban-circle:before {
                        content: "\e090";
                        }
                        .glyphicon-arrow-left:before {
                        content: "\e091";
                        }
                        .glyphicon-arrow-right:before {
                        content: "\e092";
                        }
                        .glyphicon-arrow-up:before {
                        content: "\e093";
                        }
                        .glyphicon-arrow-down:before {
                        content: "\e094";
                        }
                        .glyphicon-share-alt:before {
                        content: "\e095";
                        }
                        .glyphicon-resize-full:before {
                        content: "\e096";
                        }
                        .glyphicon-resize-small:before {
                        content: "\e097";
                        }
                        .glyphicon-exclamation-sign:before {
                        content: "\e101";
                        }
                        .glyphicon-gift:before {
                        content: "\e102";
                        }
                        .glyphicon-leaf:before {
                        content: "\e103";
                        }
                        .glyphicon-fire:before {
                        content: "\e104";
                        }
                        .glyphicon-eye-open:before {
                        content: "\e105";
                        }
                        .glyphicon-eye-close:before {
                        content: "\e106";
                        }
                        .glyphicon-warning-sign:before {
                        content: "\e107";
                        }
                        .glyphicon-plane:before {
                        content: "\e108";
                        }
                        .glyphicon-calendar:before {
                        content: "\e109";
                        }
                        .glyphicon-random:before {
                        content: "\e110";
                        }
                        .glyphicon-comment:before {
                        content: "\e111";
                        }
                        .glyphicon-magnet:before {
                        content: "\e112";
                        }
                        .glyphicon-chevron-up:before {
                        content: "\e113";
                        }
                        .glyphicon-chevron-down:before {
                        content: "\e114";
                        }
                        .glyphicon-retweet:before {
                        content: "\e115";
                        }
                        .glyphicon-shopping-cart:before {
                        content: "\e116";
                        }
                        .glyphicon-folder-close:before {
                        content: "\e117";
                        }
                        .glyphicon-folder-open:before {
                        content: "\e118";
                        }
                        .glyphicon-resize-vertical:before {
                        content: "\e119";
                        }
                        .glyphicon-resize-horizontal:before {
                        content: "\e120";
                        }
                        .glyphicon-hdd:before {
                        content: "\e121";
                        }
                        .glyphicon-bullhorn:before {
                        content: "\e122";
                        }
                        .glyphicon-bell:before {
                        content: "\e123";
                        }
                        .glyphicon-certificate:before {
                        content: "\e124";
                        }
                        .glyphicon-thumbs-up:before {
                        content: "\e125";
                        }
                        .glyphicon-thumbs-down:before {
                        content: "\e126";
                        }
                        .glyphicon-hand-right:before {
                        content: "\e127";
                        }
                        .glyphicon-hand-left:before {
                        content: "\e128";
                        }
                        .glyphicon-hand-up:before {
                        content: "\e129";
                        }
                        .glyphicon-hand-down:before {
                        content: "\e130";
                        }
                        .glyphicon-circle-arrow-right:before {
                        content: "\e131";
                        }
                        .glyphicon-circle-arrow-left:before {
                        content: "\e132";
                        }
                        .glyphicon-circle-arrow-up:before {
                        content: "\e133";
                        }
                        .glyphicon-circle-arrow-down:before {
                        content: "\e134";
                        }
                        .glyphicon-globe:before {
                        content: "\e135";
                        }
                        .glyphicon-wrench:before {
                        content: "\e136";
                        }
                        .glyphicon-tasks:before {
                        content: "\e137";
                        }
                        .glyphicon-filter:before {
                        content: "\e138";
                        }
                        .glyphicon-briefcase:before {
                        content: "\e139";
                        }
                        .glyphicon-fullscreen:before {
                        content: "\e140";
                        }
                        .glyphicon-dashboard:before {
                        content: "\e141";
                        }
                        .glyphicon-paperclip:before {
                        content: "\e142";
                        }
                        .glyphicon-heart-empty:before {
                        content: "\e143";
                        }
                        .glyphicon-link:before {
                        content: "\e144";
                        }
                        .glyphicon-phone:before {
                        content: "\e145";
                        }
                        .glyphicon-pushpin:before {
                        content: "\e146";
                        }
                        .glyphicon-usd:before {
                        content: "\e148";
                        }
                        .glyphicon-gbp:before {
                        content: "\e149";
                        }
                        .glyphicon-sort:before {
                        content: "\e150";
                        }
                        .glyphicon-sort-by-alphabet:before {
                        content: "\e151";
                        }
                        .glyphicon-sort-by-alphabet-alt:before {
                        content: "\e152";
                        }
                        .glyphicon-sort-by-order:before {
                        content: "\e153";
                        }
                        .glyphicon-sort-by-order-alt:before {
                        content: "\e154";
                        }
                        .glyphicon-sort-by-attributes:before {
                        content: "\e155";
                        }
                        .glyphicon-sort-by-attributes-alt:before {
                        content: "\e156";
                        }
                        .glyphicon-unchecked:before {
                        content: "\e157";
                        }
                        .glyphicon-expand:before {
                        content: "\e158";
                        }
                        .glyphicon-collapse-down:before {
                        content: "\e159";
                        }
                        .glyphicon-collapse-up:before {
                        content: "\e160";
                        }
                        .glyphicon-log-in:before {
                        content: "\e161";
                        }
                        .glyphicon-flash:before {
                        content: "\e162";
                        }
                        .glyphicon-log-out:before {
                        content: "\e163";
                        }
                        .glyphicon-new-window:before {
                        content: "\e164";
                        }
                        .glyphicon-record:before {
                        content: "\e165";
                        }
                        .glyphicon-save:before {
                        content: "\e166";
                        }
                        .glyphicon-open:before {
                        content: "\e167";
                        }
                        .glyphicon-saved:before {
                        content: "\e168";
                        }
                        .glyphicon-import:before {
                        content: "\e169";
                        }
                        .glyphicon-export:before {
                        content: "\e170";
                        }
                        .glyphicon-send:before {
                        content: "\e171";
                        }
                        .glyphicon-floppy-disk:before {
                        content: "\e172";
                        }
                        .glyphicon-floppy-saved:before {
                        content: "\e173";
                        }
                        .glyphicon-floppy-remove:before {
                        content: "\e174";
                        }
                        .glyphicon-floppy-save:before {
                        content: "\e175";
                        }
                        .glyphicon-floppy-open:before {
                        content: "\e176";
                        }
                        .glyphicon-credit-card:before {
                        content: "\e177";
                        }
                        .glyphicon-transfer:before {
                        content: "\e178";
                        }
                        .glyphicon-cutlery:before {
                        content: "\e179";
                        }
                        .glyphicon-header:before {
                        content: "\e180";
                        }
                        .glyphicon-compressed:before {
                        content: "\e181";
                        }
                        .glyphicon-earphone:before {
                        content: "\e182";
                        }
                        .glyphicon-phone-alt:before {
                        content: "\e183";
                        }
                        .glyphicon-tower:before {
                        content: "\e184";
                        }
                        .glyphicon-stats:before {
                        content: "\e185";
                        }
                        .glyphicon-sd-video:before {
                        content: "\e186";
                        }
                        .glyphicon-hd-video:before {
                        content: "\e187";
                        }
                        .glyphicon-subtitles:before {
                        content: "\e188";
                        }
                        .glyphicon-sound-stereo:before {
                        content: "\e189";
                        }
                        .glyphicon-sound-dolby:before {
                        content: "\e190";
                        }
                        .glyphicon-sound-5-1:before {
                        content: "\e191";
                        }
                        .glyphicon-sound-6-1:before {
                        content: "\e192";
                        }
                        .glyphicon-sound-7-1:before {
                        content: "\e193";
                        }
                        .glyphicon-copyright-mark:before {
                        content: "\e194";
                        }
                        .glyphicon-registration-mark:before {
                        content: "\e195";
                        }
                        .glyphicon-cloud-download:before {
                        content: "\e197";
                        }
                        .glyphicon-cloud-upload:before {
                        content: "\e198";
                        }
                        .glyphicon-tree-conifer:before {
                        content: "\e199";
                        }
                        .glyphicon-tree-deciduous:before {
                        content: "\e200";
                        }
                        .glyphicon-cd:before {
                        content: "\e201";
                        }
                        .glyphicon-save-file:before {
                        content: "\e202";
                        }
                        .glyphicon-open-file:before {
                        content: "\e203";
                        }
                        .glyphicon-level-up:before {
                        content: "\e204";
                        }
                        .glyphicon-copy:before {
                        content: "\e205";
                        }
                        .glyphicon-paste:before {
                        content: "\e206";
                        }
                        .glyphicon-alert:before {
                        content: "\e209";
                        }
                        .glyphicon-equalizer:before {
                        content: "\e210";
                        }
                        .glyphicon-king:before {
                        content: "\e211";
                        }
                        .glyphicon-queen:before {
                        content: "\e212";
                        }
                        .glyphicon-pawn:before {
                        content: "\e213";
                        }
                        .glyphicon-bishop:before {
                        content: "\e214";
                        }
                        .glyphicon-knight:before {
                        content: "\e215";
                        }
                        .glyphicon-baby-formula:before {
                        content: "\e216";
                        }
                        .glyphicon-tent:before {
                        content: "\26fa";
                        }
                        .glyphicon-blackboard:before {
                        content: "\e218";
                        }
                        .glyphicon-bed:before {
                        content: "\e219";
                        }
                        .glyphicon-apple:before {
                        content: "\f8ff";
                        }
                        .glyphicon-erase:before {
                        content: "\e221";
                        }
                        .glyphicon-hourglass:before {
                        content: "\231b";
                        }
                        .glyphicon-lamp:before {
                        content: "\e223";
                        }
                        .glyphicon-duplicate:before {
                        content: "\e224";
                        }
                        .glyphicon-piggy-bank:before {
                        content: "\e225";
                        }
                        .glyphicon-scissors:before {
                        content: "\e226";
                        }
                        .glyphicon-bitcoin:before {
                        content: "\e227";
                        }
                        .glyphicon-btc:before {
                        content: "\e227";
                        }
                        .glyphicon-xbt:before {
                        content: "\e227";
                        }
                        .glyphicon-yen:before {
                        content: "\00a5";
                        }
                        .glyphicon-jpy:before {
                        content: "\00a5";
                        }
                        .glyphicon-ruble:before {
                        content: "\20bd";
                        }
                        .glyphicon-rub:before {
                        content: "\20bd";
                        }
                        .glyphicon-scale:before {
                        content: "\e230";
                        }
                        .glyphicon-ice-lolly:before {
                        content: "\e231";
                        }
                        .glyphicon-ice-lolly-tasted:before {
                        content: "\e232";
                        }
                        .glyphicon-education:before {
                        content: "\e233";
                        }
                        .glyphicon-option-horizontal:before {
                        content: "\e234";
                        }
                        .glyphicon-option-vertical:before {
                        content: "\e235";
                        }
                        .glyphicon-menu-hamburger:before {
                        content: "\e236";
                        }
                        .glyphicon-modal-window:before {
                        content: "\e237";
                        }
                        .glyphicon-oil:before {
                        content: "\e238";
                        }
                        .glyphicon-grain:before {
                        content: "\e239";
                        }
                        .glyphicon-sunglasses:before {
                        content: "\e240";
                        }
                        .glyphicon-text-size:before {
                        content: "\e241";
                        }
                        .glyphicon-text-color:before {
                        content: "\e242";
                        }
                        .glyphicon-text-background:before {
                        content: "\e243";
                        }
                        .glyphicon-object-align-top:before {
                        content: "\e244";
                        }
                        .glyphicon-object-align-bottom:before {
                        content: "\e245";
                        }
                        .glyphicon-object-align-horizontal:before {
                        content: "\e246";
                        }
                        .glyphicon-object-align-left:before {
                        content: "\e247";
                        }
                        .glyphicon-object-align-vertical:before {
                        content: "\e248";
                        }
                        .glyphicon-object-align-right:before {
                        content: "\e249";
                        }
                        .glyphicon-triangle-right:before {
                        content: "\e250";
                        }
                        .glyphicon-triangle-left:before {
                        content: "\e251";
                        }
                        .glyphicon-triangle-bottom:before {
                        content: "\e252";
                        }
                        .glyphicon-triangle-top:before {
                        content: "\e253";
                        }
                        .glyphicon-console:before {
                        content: "\e254";
                        }
                        .glyphicon-superscript:before {
                        content: "\e255";
                        }
                        .glyphicon-subscript:before {
                        content: "\e256";
                        }
                        .glyphicon-menu-left:before {
                        content: "\e257";
                        }
                        .glyphicon-menu-right:before {
                        content: "\e258";
                        }
                        .glyphicon-menu-down:before {
                        content: "\e259";
                        }
                        .glyphicon-menu-up:before {
                        content: "\e260";
                        }
                        * {
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                        }
                        :after,
                        :before {
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                        }
                        html {
                        font-size: 10px;
                        -webkit-tap-highlight-color: transparent;
                        }
                        body {
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        font-size: 14px;
                        line-height: 1.42857143;
                        color: #333;
                        background-color: #fff;
                        }
                        button,
                        input,
                        select,
                        textarea {
                        font-family: inherit;
                        font-size: inherit;
                        line-height: inherit;
                        }
                        a {
                        color: #337ab7;
                        text-decoration: none;
                        }
                        a:focus,
                        a:hover {
                        color: #23527c;
                        text-decoration: underline;
                        }
                        a:focus {
                        outline: 5px auto -webkit-focus-ring-color;
                        outline-offset: -2px;
                        }
                        figure {
                        margin: 0;
                        }
                        img {
                        vertical-align: middle;
                        }
                        .carousel-inner > .item > a > img,
                        .carousel-inner > .item > img,
                        .img-responsive,
                        .thumbnail a > img,
                        .thumbnail > img {
                        display: block;
                        max-width: 100%;
                        height: auto;
                        }
                        .img-rounded {
                        border-radius: 6px;
                        }
                        .img-thumbnail {
                        display: inline-block;
                        max-width: 100%;
                        height: auto;
                        padding: 4px;
                        line-height: 1.42857143;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        -webkit-transition: all 0.2s ease-in-out;
                        -o-transition: all 0.2s ease-in-out;
                        transition: all 0.2s ease-in-out;
                        }
                        .img-circle {
                        border-radius: 50%;
                        }
                        hr {
                        margin-top: 20px;
                        margin-bottom: 20px;
                        border: 0;
                        border-top: 1px solid #eee;
                        }
                        .sr-only {
                        position: absolute;
                        width: 1px;
                        height: 1px;
                        padding: 0;
                        margin: -1px;
                        overflow: hidden;
                        clip: rect(0, 0, 0, 0);
                        border: 0;
                        }
                        .sr-only-focusable:active,
                        .sr-only-focusable:focus {
                        position: static;
                        width: auto;
                        height: auto;
                        margin: 0;
                        overflow: visible;
                        clip: auto;
                        }
                        [role="button"] {
                        cursor: pointer;
                        }
                        .h1,
                        .h2,
                        .h3,
                        .h4,
                        .h5,
                        .h6,
                        h1,
                        h2,
                        h3,
                        h4,
                        h5,
                        h6 {
                        font-family: inherit;
                        font-weight: 500;
                        line-height: 1.1;
                        color: inherit;
                        }
                        .h1 .small,
                        .h1 small,
                        .h2 .small,
                        .h2 small,
                        .h3 .small,
                        .h3 small,
                        .h4 .small,
                        .h4 small,
                        .h5 .small,
                        .h5 small,
                        .h6 .small,
                        .h6 small,
                        h1 .small,
                        h1 small,
                        h2 .small,
                        h2 small,
                        h3 .small,
                        h3 small,
                        h4 .small,
                        h4 small,
                        h5 .small,
                        h5 small,
                        h6 .small,
                        h6 small {
                        font-weight: 400;
                        line-height: 1;
                        color: #777;
                        }
                        .h1,
                        .h2,
                        .h3,
                        h1,
                        h2,
                        h3 {
                        margin-top: 20px;
                        margin-bottom: 10px;
                        }
                        .h1 .small,
                        .h1 small,
                        .h2 .small,
                        .h2 small,
                        .h3 .small,
                        .h3 small,
                        h1 .small,
                        h1 small,
                        h2 .small,
                        h2 small,
                        h3 .small,
                        h3 small {
                        font-size: 65%;
                        }
                        .h4,
                        .h5,
                        .h6,
                        h4,
                        h5,
                        h6 {
                        margin-top: 10px;
                        margin-bottom: 10px;
                        }
                        .h4 .small,
                        .h4 small,
                        .h5 .small,
                        .h5 small,
                        .h6 .small,
                        .h6 small,
                        h4 .small,
                        h4 small,
                        h5 .small,
                        h5 small,
                        h6 .small,
                        h6 small {
                        font-size: 75%;
                        }
                        .h1,
                        h1 {
                        font-size: 36px;
                        }
                        .h2,
                        h2 {
                        font-size: 30px;
                        }
                        .h3,
                        h3 {
                        font-size: 24px;
                        }
                        .h4,
                        h4 {
                        font-size: 18px;
                        }
                        .h5,
                        h5 {
                        font-size: 14px;
                        }
                        .h6,
                        h6 {
                        font-size: 12px;
                        }
                        p {
                        margin: 0 0 10px;
                        }
                        .lead {
                        margin-bottom: 20px;
                        font-size: 16px;
                        font-weight: 300;
                        line-height: 1.4;
                        }
                        @media (min-width: 768px) {
                        .lead {
                            font-size: 21px;
                        }
                        }
                        .small,
                        small {
                        font-size: 85%;
                        }
                        .mark,
                        mark {
                        padding: 0.2em;
                        background-color: #fcf8e3;
                        }
                        .text-left {
                        text-align: left;
                        }
                        .text-right {
                        text-align: right;
                        }
                        .text-center {
                        text-align: center;
                        }
                        .text-justify {
                        text-align: justify;
                        }
                        .text-nowrap {
                        white-space: nowrap;
                        }
                        .text-lowercase {
                        text-transform: lowercase;
                        }
                        .text-uppercase {
                        text-transform: uppercase;
                        }
                        .text-capitalize {
                        text-transform: capitalize;
                        }
                        .text-muted {
                        color: #777;
                        }
                        .text-primary {
                        color: #337ab7;
                        }
                        a.text-primary:focus,
                        a.text-primary:hover {
                        color: #286090;
                        }
                        .text-success {
                        color: #3c763d;
                        }
                        a.text-success:focus,
                        a.text-success:hover {
                        color: #2b542c;
                        }
                        .text-info {
                        color: #31708f;
                        }
                        a.text-info:focus,
                        a.text-info:hover {
                        color: #245269;
                        }
                        .text-warning {
                        color: #8a6d3b;
                        }
                        a.text-warning:focus,
                        a.text-warning:hover {
                        color: #66512c;
                        }
                        .text-danger {
                        color: #a94442;
                        }
                        a.text-danger:focus,
                        a.text-danger:hover {
                        color: #843534;
                        }
                        .bg-primary {
                        color: #fff;
                        background-color: #337ab7;
                        }
                        a.bg-primary:focus,
                        a.bg-primary:hover {
                        background-color: #286090;
                        }
                        .bg-success {
                        background-color: #dff0d8;
                        }
                        a.bg-success:focus,
                        a.bg-success:hover {
                        background-color: #c1e2b3;
                        }
                        .bg-info {
                        background-color: #d9edf7;
                        }
                        a.bg-info:focus,
                        a.bg-info:hover {
                        background-color: #afd9ee;
                        }
                        .bg-warning {
                        background-color: #fcf8e3;
                        }
                        a.bg-warning:focus,
                        a.bg-warning:hover {
                        background-color: #f7ecb5;
                        }
                        .bg-danger {
                        background-color: #f2dede;
                        }
                        a.bg-danger:focus,
                        a.bg-danger:hover {
                        background-color: #e4b9b9;
                        }
                        .page-header {
                        padding-bottom: 9px;
                        margin: 40px 0 20px;
                        border-bottom: 1px solid #eee;
                        }
                        ol,
                        ul {
                        margin-top: 0;
                        margin-bottom: 10px;
                        }
                        ol ol,
                        ol ul,
                        ul ol,
                        ul ul {
                        margin-bottom: 0;
                        }
                        .list-unstyled {
                        padding-left: 0;
                        list-style: none;
                        }
                        .list-inline {
                        padding-left: 0;
                        margin-left: -5px;
                        list-style: none;
                        }
                        .list-inline > li {
                        display: inline-block;
                        padding-right: 5px;
                        padding-left: 5px;
                        }
                        dl {
                        margin-top: 0;
                        margin-bottom: 20px;
                        }
                        dd,
                        dt {
                        line-height: 1.42857143;
                        }
                        dt {
                        font-weight: 700;
                        }
                        dd {
                        margin-left: 0;
                        }
                        @media (min-width: 768px) {
                        .dl-horizontal dt {
                            float: left;
                            width: 160px;
                            overflow: hidden;
                            clear: left;
                            text-align: right;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                        }
                        .dl-horizontal dd {
                            margin-left: 180px;
                        }
                        }
                        abbr[data-original-title],
                        abbr[title] {
                        cursor: help;
                        border-bottom: 1px dotted #777;
                        }
                        .initialism {
                        font-size: 90%;
                        text-transform: uppercase;
                        }
                        blockquote {
                        padding: 10px 20px;
                        margin: 0 0 20px;
                        font-size: 17.5px;
                        border-left: 5px solid #eee;
                        }
                        blockquote ol:last-child,
                        blockquote p:last-child,
                        blockquote ul:last-child {
                        margin-bottom: 0;
                        }
                        blockquote .small,
                        blockquote footer,
                        blockquote small {
                        display: block;
                        font-size: 80%;
                        line-height: 1.42857143;
                        color: #777;
                        }
                        blockquote .small:before,
                        blockquote footer:before,
                        blockquote small:before {
                        content: "\2014 \00A0";
                        }
                        .blockquote-reverse,
                        blockquote.pull-right {
                        padding-right: 15px;
                        padding-left: 0;
                        text-align: right;
                        border-right: 5px solid #eee;
                        border-left: 0;
                        }
                        .blockquote-reverse .small:before,
                        .blockquote-reverse footer:before,
                        .blockquote-reverse small:before,
                        blockquote.pull-right .small:before,
                        blockquote.pull-right footer:before,
                        blockquote.pull-right small:before {
                        content: "";
                        }
                        .blockquote-reverse .small:after,
                        .blockquote-reverse footer:after,
                        .blockquote-reverse small:after,
                        blockquote.pull-right .small:after,
                        blockquote.pull-right footer:after,
                        blockquote.pull-right small:after {
                        content: "\00A0 \2014";
                        }
                        address {
                        margin-bottom: 20px;
                        font-style: normal;
                        line-height: 1.42857143;
                        }
                        code,
                        kbd,
                        pre,
                        samp {
                        font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
                        }
                        code {
                        padding: 2px 4px;
                        font-size: 90%;
                        color: #c7254e;
                        background-color: #f9f2f4;
                        border-radius: 4px;
                        }
                        kbd {
                        padding: 2px 4px;
                        font-size: 90%;
                        color: #fff;
                        background-color: #333;
                        border-radius: 3px;
                        -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.25);
                        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.25);
                        }
                        kbd kbd {
                        padding: 0;
                        font-size: 100%;
                        font-weight: 700;
                        -webkit-box-shadow: none;
                        box-shadow: none;
                        }
                        pre {
                        display: block;
                        padding: 9.5px;
                        margin: 0 0 10px;
                        font-size: 13px;
                        line-height: 1.42857143;
                        color: #333;
                        word-break: break-all;
                        word-wrap: break-word;
                        background-color: #f5f5f5;
                        border: 1px solid #ccc;
                        border-radius: 4px;
                        }
                        pre code {
                        padding: 0;
                        font-size: inherit;
                        color: inherit;
                        white-space: pre-wrap;
                        background-color: transparent;
                        border-radius: 0;
                        }
                        .pre-scrollable {
                        max-height: 340px;
                        overflow-y: scroll;
                        }
                        .container {
                        padding-right: 15px;
                        padding-left: 15px;
                        margin-right: auto;
                        margin-left: auto;
                        }
                        @media (min-width: 768px) {
                        .container {
                            width: 750px;
                        }
                        }
                        @media (min-width: 992px) {
                        .container {
                            width: 970px;
                        }
                        }
                        @media (min-width: 1200px) {
                        .container {
                            width: 1170px;
                        }
                        }
                        .container-fluid {
                        padding-right: 15px;
                        padding-left: 15px;
                        margin-right: auto;
                        margin-left: auto;
                        }
                        .row {
                        margin-right: -15px;
                        margin-left: -15px;
                        }
                        .col-lg-1,
                        .col-lg-10,
                        .col-lg-11,
                        .col-lg-12,
                        .col-lg-2,
                        .col-lg-3,
                        .col-lg-4,
                        .col-lg-5,
                        .col-lg-6,
                        .col-lg-7,
                        .col-lg-8,
                        .col-lg-9,
                        .col-md-1,
                        .col-md-10,
                        .col-md-11,
                        .col-md-12,
                        .col-md-2,
                        .col-md-3,
                        .col-md-4,
                        .col-md-5,
                        .col-md-6,
                        .col-md-7,
                        .col-md-8,
                        .col-md-9,
                        .col-sm-1,
                        .col-sm-10,
                        .col-sm-11,
                        .col-sm-12,
                        .col-sm-2,
                        .col-sm-3,
                        .col-sm-4,
                        .col-sm-5,
                        .col-sm-6,
                        .col-sm-7,
                        .col-sm-8,
                        .col-sm-9,
                        .col-xs-1,
                        .col-xs-10,
                        .col-xs-11,
                        .col-xs-12,
                        .col-xs-2,
                        .col-xs-3,
                        .col-xs-4,
                        .col-xs-5,
                        .col-xs-6,
                        .col-xs-7,
                        .col-xs-8,
                        .col-xs-9 {
                        position: relative;
                        min-height: 1px;
                        padding-right: 15px;
                        padding-left: 15px;
                        }
                        .col-xs-1,
                        .col-xs-10,
                        .col-xs-11,
                        .col-xs-12,
                        .col-xs-2,
                        .col-xs-3,
                        .col-xs-4,
                        .col-xs-5,
                        .col-xs-6,
                        .col-xs-7,
                        .col-xs-8,
                        .col-xs-9 {
                        float: left;
                        }
                        .col-xs-12 {
                        width: 100%;
                        }
                        .col-xs-11 {
                        width: 91.66666667%;
                        }
                        .col-xs-10 {
                        width: 83.33333333%;
                        }
                        .col-xs-9 {
                        width: 75%;
                        }
                        .col-xs-8 {
                        width: 66.66666667%;
                        }
                        .col-xs-7 {
                        width: 58.33333333%;
                        }
                        .col-xs-6 {
                        width: 50%;
                        }
                        .col-xs-5 {
                        width: 41.66666667%;
                        }
                        .col-xs-4 {
                        width: 33.33333333%;
                        }
                        .col-xs-3 {
                        width: 25%;
                        }
                        .col-xs-2 {
                        width: 16.66666667%;
                        }
                        .col-xs-1 {
                        width: 8.33333333%;
                        }
                        .col-xs-pull-12 {
                        right: 100%;
                        }
                        .col-xs-pull-11 {
                        right: 91.66666667%;
                        }
                        .col-xs-pull-10 {
                        right: 83.33333333%;
                        }
                        .col-xs-pull-9 {
                        right: 75%;
                        }
                        .col-xs-pull-8 {
                        right: 66.66666667%;
                        }
                        .col-xs-pull-7 {
                        right: 58.33333333%;
                        }
                        .col-xs-pull-6 {
                        right: 50%;
                        }
                        .col-xs-pull-5 {
                        right: 41.66666667%;
                        }
                        .col-xs-pull-4 {
                        right: 33.33333333%;
                        }
                        .col-xs-pull-3 {
                        right: 25%;
                        }
                        .col-xs-pull-2 {
                        right: 16.66666667%;
                        }
                        .col-xs-pull-1 {
                        right: 8.33333333%;
                        }
                        .col-xs-pull-0 {
                        right: auto;
                        }
                        .col-xs-push-12 {
                        left: 100%;
                        }
                        .col-xs-push-11 {
                        left: 91.66666667%;
                        }
                        .col-xs-push-10 {
                        left: 83.33333333%;
                        }
                        .col-xs-push-9 {
                        left: 75%;
                        }
                        .col-xs-push-8 {
                        left: 66.66666667%;
                        }
                        .col-xs-push-7 {
                        left: 58.33333333%;
                        }
                        .col-xs-push-6 {
                        left: 50%;
                        }
                        .col-xs-push-5 {
                        left: 41.66666667%;
                        }
                        .col-xs-push-4 {
                        left: 33.33333333%;
                        }
                        .col-xs-push-3 {
                        left: 25%;
                        }
                        .col-xs-push-2 {
                        left: 16.66666667%;
                        }
                        .col-xs-push-1 {
                        left: 8.33333333%;
                        }
                        .col-xs-push-0 {
                        left: auto;
                        }
                        .col-xs-offset-12 {
                        margin-left: 100%;
                        }
                        .col-xs-offset-11 {
                        margin-left: 91.66666667%;
                        }
                        .col-xs-offset-10 {
                        margin-left: 83.33333333%;
                        }
                        .col-xs-offset-9 {
                        margin-left: 75%;
                        }
                        .col-xs-offset-8 {
                        margin-left: 66.66666667%;
                        }
                        .col-xs-offset-7 {
                        margin-left: 58.33333333%;
                        }
                        .col-xs-offset-6 {
                        margin-left: 50%;
                        }
                        .col-xs-offset-5 {
                        margin-left: 41.66666667%;
                        }
                        .col-xs-offset-4 {
                        margin-left: 33.33333333%;
                        }
                        .col-xs-offset-3 {
                        margin-left: 25%;
                        }
                        .col-xs-offset-2 {
                        margin-left: 16.66666667%;
                        }
                        .col-xs-offset-1 {
                        margin-left: 8.33333333%;
                        }
                        .col-xs-offset-0 {
                        margin-left: 0;
                        }
                        @media (min-width: 768px) {
                        .col-sm-1,
                        .col-sm-10,
                        .col-sm-11,
                        .col-sm-12,
                        .col-sm-2,
                        .col-sm-3,
                        .col-sm-4,
                        .col-sm-5,
                        .col-sm-6,
                        .col-sm-7,
                        .col-sm-8,
                        .col-sm-9 {
                            float: left;
                        }
                        .col-sm-12 {
                            width: 100%;
                        }
                        .col-sm-11 {
                            width: 91.66666667%;
                        }
                        .col-sm-10 {
                            width: 83.33333333%;
                        }
                        .col-sm-9 {
                            width: 75%;
                        }
                        .col-sm-8 {
                            width: 66.66666667%;
                        }
                        .col-sm-7 {
                            width: 58.33333333%;
                        }
                        .col-sm-6 {
                            width: 50%;
                        }
                        .col-sm-5 {
                            width: 41.66666667%;
                        }
                        .col-sm-4 {
                            width: 33.33333333%;
                        }
                        .col-sm-3 {
                            width: 25%;
                        }
                        .col-sm-2 {
                            width: 16.66666667%;
                        }
                        .col-sm-1 {
                            width: 8.33333333%;
                        }
                        .col-sm-pull-12 {
                            right: 100%;
                        }
                        .col-sm-pull-11 {
                            right: 91.66666667%;
                        }
                        .col-sm-pull-10 {
                            right: 83.33333333%;
                        }
                        .col-sm-pull-9 {
                            right: 75%;
                        }
                        .col-sm-pull-8 {
                            right: 66.66666667%;
                        }
                        .col-sm-pull-7 {
                            right: 58.33333333%;
                        }
                        .col-sm-pull-6 {
                            right: 50%;
                        }
                        .col-sm-pull-5 {
                            right: 41.66666667%;
                        }
                        .col-sm-pull-4 {
                            right: 33.33333333%;
                        }
                        .col-sm-pull-3 {
                            right: 25%;
                        }
                        .col-sm-pull-2 {
                            right: 16.66666667%;
                        }
                        .col-sm-pull-1 {
                            right: 8.33333333%;
                        }
                        .col-sm-pull-0 {
                            right: auto;
                        }
                        .col-sm-push-12 {
                            left: 100%;
                        }
                        .col-sm-push-11 {
                            left: 91.66666667%;
                        }
                        .col-sm-push-10 {
                            left: 83.33333333%;
                        }
                        .col-sm-push-9 {
                            left: 75%;
                        }
                        .col-sm-push-8 {
                            left: 66.66666667%;
                        }
                        .col-sm-push-7 {
                            left: 58.33333333%;
                        }
                        .col-sm-push-6 {
                            left: 50%;
                        }
                        .col-sm-push-5 {
                            left: 41.66666667%;
                        }
                        .col-sm-push-4 {
                            left: 33.33333333%;
                        }
                        .col-sm-push-3 {
                            left: 25%;
                        }
                        .col-sm-push-2 {
                            left: 16.66666667%;
                        }
                        .col-sm-push-1 {
                            left: 8.33333333%;
                        }
                        .col-sm-push-0 {
                            left: auto;
                        }
                        .col-sm-offset-12 {
                            margin-left: 100%;
                        }
                        .col-sm-offset-11 {
                            margin-left: 91.66666667%;
                        }
                        .col-sm-offset-10 {
                            margin-left: 83.33333333%;
                        }
                        .col-sm-offset-9 {
                            margin-left: 75%;
                        }
                        .col-sm-offset-8 {
                            margin-left: 66.66666667%;
                        }
                        .col-sm-offset-7 {
                            margin-left: 58.33333333%;
                        }
                        .col-sm-offset-6 {
                            margin-left: 50%;
                        }
                        .col-sm-offset-5 {
                            margin-left: 41.66666667%;
                        }
                        .col-sm-offset-4 {
                            margin-left: 33.33333333%;
                        }
                        .col-sm-offset-3 {
                            margin-left: 25%;
                        }
                        .col-sm-offset-2 {
                            margin-left: 16.66666667%;
                        }
                        .col-sm-offset-1 {
                            margin-left: 8.33333333%;
                        }
                        .col-sm-offset-0 {
                            margin-left: 0;
                        }
                        }
                        @media (min-width: 992px) {
                        .col-md-1,
                        .col-md-10,
                        .col-md-11,
                        .col-md-12,
                        .col-md-2,
                        .col-md-3,
                        .col-md-4,
                        .col-md-5,
                        .col-md-6,
                        .col-md-7,
                        .col-md-8,
                        .col-md-9 {
                            float: left;
                        }
                        .col-md-12 {
                            width: 100%;
                        }
                        .col-md-11 {
                            width: 91.66666667%;
                        }
                        .col-md-10 {
                            width: 83.33333333%;
                        }
                        .col-md-9 {
                            width: 75%;
                        }
                        .col-md-8 {
                            width: 66.66666667%;
                        }
                        .col-md-7 {
                            width: 58.33333333%;
                        }
                        .col-md-6 {
                            width: 50%;
                        }
                        .col-md-5 {
                            width: 41.66666667%;
                        }
                        .col-md-4 {
                            width: 33.33333333%;
                        }
                        .col-md-3 {
                            width: 25%;
                        }
                        .col-md-2 {
                            width: 16.66666667%;
                        }
                        .col-md-1 {
                            width: 8.33333333%;
                        }
                        .col-md-pull-12 {
                            right: 100%;
                        }
                        .col-md-pull-11 {
                            right: 91.66666667%;
                        }
                        .col-md-pull-10 {
                            right: 83.33333333%;
                        }
                        .col-md-pull-9 {
                            right: 75%;
                        }
                        .col-md-pull-8 {
                            right: 66.66666667%;
                        }
                        .col-md-pull-7 {
                            right: 58.33333333%;
                        }
                        .col-md-pull-6 {
                            right: 50%;
                        }
                        .col-md-pull-5 {
                            right: 41.66666667%;
                        }
                        .col-md-pull-4 {
                            right: 33.33333333%;
                        }
                        .col-md-pull-3 {
                            right: 25%;
                        }
                        .col-md-pull-2 {
                            right: 16.66666667%;
                        }
                        .col-md-pull-1 {
                            right: 8.33333333%;
                        }
                        .col-md-pull-0 {
                            right: auto;
                        }
                        .col-md-push-12 {
                            left: 100%;
                        }
                        .col-md-push-11 {
                            left: 91.66666667%;
                        }
                        .col-md-push-10 {
                            left: 83.33333333%;
                        }
                        .col-md-push-9 {
                            left: 75%;
                        }
                        .col-md-push-8 {
                            left: 66.66666667%;
                        }
                        .col-md-push-7 {
                            left: 58.33333333%;
                        }
                        .col-md-push-6 {
                            left: 50%;
                        }
                        .col-md-push-5 {
                            left: 41.66666667%;
                        }
                        .col-md-push-4 {
                            left: 33.33333333%;
                        }
                        .col-md-push-3 {
                            left: 25%;
                        }
                        .col-md-push-2 {
                            left: 16.66666667%;
                        }
                        .col-md-push-1 {
                            left: 8.33333333%;
                        }
                        .col-md-push-0 {
                            left: auto;
                        }
                        .col-md-offset-12 {
                            margin-left: 100%;
                        }
                        .col-md-offset-11 {
                            margin-left: 91.66666667%;
                        }
                        .col-md-offset-10 {
                            margin-left: 83.33333333%;
                        }
                        .col-md-offset-9 {
                            margin-left: 75%;
                        }
                        .col-md-offset-8 {
                            margin-left: 66.66666667%;
                        }
                        .col-md-offset-7 {
                            margin-left: 58.33333333%;
                        }
                        .col-md-offset-6 {
                            margin-left: 50%;
                        }
                        .col-md-offset-5 {
                            margin-left: 41.66666667%;
                        }
                        .col-md-offset-4 {
                            margin-left: 33.33333333%;
                        }
                        .col-md-offset-3 {
                            margin-left: 25%;
                        }
                        .col-md-offset-2 {
                            margin-left: 16.66666667%;
                        }
                        .col-md-offset-1 {
                            margin-left: 8.33333333%;
                        }
                        .col-md-offset-0 {
                            margin-left: 0;
                        }
                        }
                        @media (min-width: 1200px) {
                        .col-lg-1,
                        .col-lg-10,
                        .col-lg-11,
                        .col-lg-12,
                        .col-lg-2,
                        .col-lg-3,
                        .col-lg-4,
                        .col-lg-5,
                        .col-lg-6,
                        .col-lg-7,
                        .col-lg-8,
                        .col-lg-9 {
                            float: left;
                        }
                        .col-lg-12 {
                            width: 100%;
                        }
                        .col-lg-11 {
                            width: 91.66666667%;
                        }
                        .col-lg-10 {
                            width: 83.33333333%;
                        }
                        .col-lg-9 {
                            width: 75%;
                        }
                        .col-lg-8 {
                            width: 66.66666667%;
                        }
                        .col-lg-7 {
                            width: 58.33333333%;
                        }
                        .col-lg-6 {
                            width: 50%;
                        }
                        .col-lg-5 {
                            width: 41.66666667%;
                        }
                        .col-lg-4 {
                            width: 33.33333333%;
                        }
                        .col-lg-3 {
                            width: 25%;
                        }
                        .col-lg-2 {
                            width: 16.66666667%;
                        }
                        .col-lg-1 {
                            width: 8.33333333%;
                        }
                        .col-lg-pull-12 {
                            right: 100%;
                        }
                        .col-lg-pull-11 {
                            right: 91.66666667%;
                        }
                        .col-lg-pull-10 {
                            right: 83.33333333%;
                        }
                        .col-lg-pull-9 {
                            right: 75%;
                        }
                        .col-lg-pull-8 {
                            right: 66.66666667%;
                        }
                        .col-lg-pull-7 {
                            right: 58.33333333%;
                        }
                        .col-lg-pull-6 {
                            right: 50%;
                        }
                        .col-lg-pull-5 {
                            right: 41.66666667%;
                        }
                        .col-lg-pull-4 {
                            right: 33.33333333%;
                        }
                        .col-lg-pull-3 {
                            right: 25%;
                        }
                        .col-lg-pull-2 {
                            right: 16.66666667%;
                        }
                        .col-lg-pull-1 {
                            right: 8.33333333%;
                        }
                        .col-lg-pull-0 {
                            right: auto;
                        }
                        .col-lg-push-12 {
                            left: 100%;
                        }
                        .col-lg-push-11 {
                            left: 91.66666667%;
                        }
                        .col-lg-push-10 {
                            left: 83.33333333%;
                        }
                        .col-lg-push-9 {
                            left: 75%;
                        }
                        .col-lg-push-8 {
                            left: 66.66666667%;
                        }
                        .col-lg-push-7 {
                            left: 58.33333333%;
                        }
                        .col-lg-push-6 {
                            left: 50%;
                        }
                        .col-lg-push-5 {
                            left: 41.66666667%;
                        }
                        .col-lg-push-4 {
                            left: 33.33333333%;
                        }
                        .col-lg-push-3 {
                            left: 25%;
                        }
                        .col-lg-push-2 {
                            left: 16.66666667%;
                        }
                        .col-lg-push-1 {
                            left: 8.33333333%;
                        }
                        .col-lg-push-0 {
                            left: auto;
                        }
                        .col-lg-offset-12 {
                            margin-left: 100%;
                        }
                        .col-lg-offset-11 {
                            margin-left: 91.66666667%;
                        }
                        .col-lg-offset-10 {
                            margin-left: 83.33333333%;
                        }
                        .col-lg-offset-9 {
                            margin-left: 75%;
                        }
                        .col-lg-offset-8 {
                            margin-left: 66.66666667%;
                        }
                        .col-lg-offset-7 {
                            margin-left: 58.33333333%;
                        }
                        .col-lg-offset-6 {
                            margin-left: 50%;
                        }
                        .col-lg-offset-5 {
                            margin-left: 41.66666667%;
                        }
                        .col-lg-offset-4 {
                            margin-left: 33.33333333%;
                        }
                        .col-lg-offset-3 {
                            margin-left: 25%;
                        }
                        .col-lg-offset-2 {
                            margin-left: 16.66666667%;
                        }
                        .col-lg-offset-1 {
                            margin-left: 8.33333333%;
                        }
                        .col-lg-offset-0 {
                            margin-left: 0;
                        }
                        }
                        table {
                        background-color: transparent;
                        }
                        caption {
                        padding-top: 8px;
                        padding-bottom: 8px;
                        color: #777;
                        text-align: left;
                        }
                        th {
                        text-align: left;
                        }
                        .table {
                        width: 100%;
                        max-width: 100%;
                        margin-bottom: 20px;
                        }
                        .table > tbody > tr > td,
                        .table > tbody > tr > th,
                        .table > tfoot > tr > td,
                        .table > tfoot > tr > th,
                        .table > thead > tr > td,
                        .table > thead > tr > th {
                        padding: 8px;
                        line-height: 1.42857143;
                        vertical-align: top;
                        border-top: 1px solid #ddd;
                        }
                        .table > thead > tr > th {
                        vertical-align: bottom;
                        border-bottom: 2px solid #ddd;
                        }
                        .table > caption + thead > tr:first-child > td,
                        .table > caption + thead > tr:first-child > th,
                        .table > colgroup + thead > tr:first-child > td,
                        .table > colgroup + thead > tr:first-child > th,
                        .table > thead:first-child > tr:first-child > td,
                        .table > thead:first-child > tr:first-child > th {
                        border-top: 0;
                        }
                        .table > tbody + tbody {
                        border-top: 2px solid #ddd;
                        }
                        .table .table {
                        background-color: #fff;
                        }
                        .table-condensed > tbody > tr > td,
                        .table-condensed > tbody > tr > th,
                        .table-condensed > tfoot > tr > td,
                        .table-condensed > tfoot > tr > th,
                        .table-condensed > thead > tr > td,
                        .table-condensed > thead > tr > th {
                        padding: 5px;
                        }
                        .table-bordered {
                        border: 1px solid #ddd;
                        }
                        .table-bordered > tbody > tr > td,
                        .table-bordered > tbody > tr > th,
                        .table-bordered > tfoot > tr > td,
                        .table-bordered > tfoot > tr > th,
                        .table-bordered > thead > tr > td,
                        .table-bordered > thead > tr > th {
                        border: 1px solid #ddd;
                        }
                        .table-bordered > thead > tr > td,
                        .table-bordered > thead > tr > th {
                        border-bottom-width: 2px;
                        }
                        .table-striped > tbody > tr:nth-of-type(odd) {
                        background-color: #f9f9f9;
                        }
                        .table-hover > tbody > tr:hover {
                        background-color: #f5f5f5;
                        }
                        table col[class*="col-"] {
                        position: static;
                        display: table-column;
                        float: none;
                        }
                        table td[class*="col-"],
                        table th[class*="col-"] {
                        position: static;
                        display: table-cell;
                        float: none;
                        }
                        .table > tbody > tr.active > td,
                        .table > tbody > tr.active > th,
                        .table > tbody > tr > td.active,
                        .table > tbody > tr > th.active,
                        .table > tfoot > tr.active > td,
                        .table > tfoot > tr.active > th,
                        .table > tfoot > tr > td.active,
                        .table > tfoot > tr > th.active,
                        .table > thead > tr.active > td,
                        .table > thead > tr.active > th,
                        .table > thead > tr > td.active,
                        .table > thead > tr > th.active {
                        background-color: #f5f5f5;
                        }
                        .table-hover > tbody > tr.active:hover > td,
                        .table-hover > tbody > tr.active:hover > th,
                        .table-hover > tbody > tr:hover > .active,
                        .table-hover > tbody > tr > td.active:hover,
                        .table-hover > tbody > tr > th.active:hover {
                        background-color: #e8e8e8;
                        }
                        .table > tbody > tr.success > td,
                        .table > tbody > tr.success > th,
                        .table > tbody > tr > td.success,
                        .table > tbody > tr > th.success,
                        .table > tfoot > tr.success > td,
                        .table > tfoot > tr.success > th,
                        .table > tfoot > tr > td.success,
                        .table > tfoot > tr > th.success,
                        .table > thead > tr.success > td,
                        .table > thead > tr.success > th,
                        .table > thead > tr > td.success,
                        .table > thead > tr > th.success {
                        background-color: #dff0d8;
                        }
                        .table-hover > tbody > tr.success:hover > td,
                        .table-hover > tbody > tr.success:hover > th,
                        .table-hover > tbody > tr:hover > .success,
                        .table-hover > tbody > tr > td.success:hover,
                        .table-hover > tbody > tr > th.success:hover {
                        background-color: #d0e9c6;
                        }
                        .table > tbody > tr.info > td,
                        .table > tbody > tr.info > th,
                        .table > tbody > tr > td.info,
                        .table > tbody > tr > th.info,
                        .table > tfoot > tr.info > td,
                        .table > tfoot > tr.info > th,
                        .table > tfoot > tr > td.info,
                        .table > tfoot > tr > th.info,
                        .table > thead > tr.info > td,
                        .table > thead > tr.info > th,
                        .table > thead > tr > td.info,
                        .table > thead > tr > th.info {
                        background-color: #d9edf7;
                        }
                        .table-hover > tbody > tr.info:hover > td,
                        .table-hover > tbody > tr.info:hover > th,
                        .table-hover > tbody > tr:hover > .info,
                        .table-hover > tbody > tr > td.info:hover,
                        .table-hover > tbody > tr > th.info:hover {
                        background-color: #c4e3f3;
                        }
                        .table > tbody > tr.warning > td,
                        .table > tbody > tr.warning > th,
                        .table > tbody > tr > td.warning,
                        .table > tbody > tr > th.warning,
                        .table > tfoot > tr.warning > td,
                        .table > tfoot > tr.warning > th,
                        .table > tfoot > tr > td.warning,
                        .table > tfoot > tr > th.warning,
                        .table > thead > tr.warning > td,
                        .table > thead > tr.warning > th,
                        .table > thead > tr > td.warning,
                        .table > thead > tr > th.warning {
                        background-color: #fcf8e3;
                        }
                        .table-hover > tbody > tr.warning:hover > td,
                        .table-hover > tbody > tr.warning:hover > th,
                        .table-hover > tbody > tr:hover > .warning,
                        .table-hover > tbody > tr > td.warning:hover,
                        .table-hover > tbody > tr > th.warning:hover {
                        background-color: #faf2cc;
                        }
                        .table > tbody > tr.danger > td,
                        .table > tbody > tr.danger > th,
                        .table > tbody > tr > td.danger,
                        .table > tbody > tr > th.danger,
                        .table > tfoot > tr.danger > td,
                        .table > tfoot > tr.danger > th,
                        .table > tfoot > tr > td.danger,
                        .table > tfoot > tr > th.danger,
                        .table > thead > tr.danger > td,
                        .table > thead > tr.danger > th,
                        .table > thead > tr > td.danger,
                        .table > thead > tr > th.danger {
                        background-color: #f2dede;
                        }
                        .table-hover > tbody > tr.danger:hover > td,
                        .table-hover > tbody > tr.danger:hover > th,
                        .table-hover > tbody > tr:hover > .danger,
                        .table-hover > tbody > tr > td.danger:hover,
                        .table-hover > tbody > tr > th.danger:hover {
                        background-color: #ebcccc;
                        }
                        .table-responsive {
                        min-height: 0.01%;
                        overflow-x: auto;
                        }
                        @media screen and (max-width: 767px) {
                        .table-responsive {
                            width: 100%;
                            margin-bottom: 15px;
                            overflow-y: hidden;
                            -ms-overflow-style: -ms-autohiding-scrollbar;
                            border: 1px solid #ddd;
                        }
                        .table-responsive > .table {
                            margin-bottom: 0;
                        }
                        .table-responsive > .table > tbody > tr > td,
                        .table-responsive > .table > tbody > tr > th,
                        .table-responsive > .table > tfoot > tr > td,
                        .table-responsive > .table > tfoot > tr > th,
                        .table-responsive > .table > thead > tr > td,
                        .table-responsive > .table > thead > tr > th {
                            white-space: nowrap;
                        }
                        .table-responsive > .table-bordered {
                            border: 0;
                        }
                        .table-responsive > .table-bordered > tbody > tr > td:first-child,
                        .table-responsive > .table-bordered > tbody > tr > th:first-child,
                        .table-responsive > .table-bordered > tfoot > tr > td:first-child,
                        .table-responsive > .table-bordered > tfoot > tr > th:first-child,
                        .table-responsive > .table-bordered > thead > tr > td:first-child,
                        .table-responsive > .table-bordered > thead > tr > th:first-child {
                            border-left: 0;
                        }
                        .table-responsive > .table-bordered > tbody > tr > td:last-child,
                        .table-responsive > .table-bordered > tbody > tr > th:last-child,
                        .table-responsive > .table-bordered > tfoot > tr > td:last-child,
                        .table-responsive > .table-bordered > tfoot > tr > th:last-child,
                        .table-responsive > .table-bordered > thead > tr > td:last-child,
                        .table-responsive > .table-bordered > thead > tr > th:last-child {
                            border-right: 0;
                        }
                        .table-responsive > .table-bordered > tbody > tr:last-child > td,
                        .table-responsive > .table-bordered > tbody > tr:last-child > th,
                        .table-responsive > .table-bordered > tfoot > tr:last-child > td,
                        .table-responsive > .table-bordered > tfoot > tr:last-child > th {
                            border-bottom: 0;
                        }
                        }
                        fieldset {
                        min-width: 0;
                        padding: 0;
                        margin: 0;
                        border: 0;
                        }
                        legend {
                        display: block;
                        width: 100%;
                        padding: 0;
                        margin-bottom: 20px;
                        font-size: 21px;
                        line-height: inherit;
                        color: #333;
                        border: 0;
                        border-bottom: 1px solid #e5e5e5;
                        }
                        label {
                        display: inline-block;
                        max-width: 100%;
                        margin-bottom: 5px;
                        font-weight: 700;
                        }
                        input[type="search"] {
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                        }
                        input[type="checkbox"],
                        input[type="radio"] {
                        margin: 4px 0 0;
                        line-height: normal;
                        }
                        input[type="file"] {
                        display: block;
                        }
                        input[type="range"] {
                        display: block;
                        width: 100%;
                        }
                        select[multiple],
                        select[size] {
                        height: auto;
                        }
                        input[type="checkbox"]:focus,
                        input[type="file"]:focus,
                        input[type="radio"]:focus {
                        outline: 5px auto -webkit-focus-ring-color;
                        outline-offset: -2px;
                        }
                        output {
                        display: block;
                        padding-top: 7px;
                        font-size: 14px;
                        line-height: 1.42857143;
                        color: #555;
                        }
                        .form-control {
                        display: block;
                        width: 100%;
                        height: 34px;
                        padding: 6px 12px;
                        font-size: 14px;
                        line-height: 1.42857143;
                        color: #555;
                        background-color: #fff;
                        background-image: none;
                        border: 1px solid #ccc;
                        border-radius: 4px;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                        -webkit-transition: border-color ease-in-out 0.15s,
                            -webkit-box-shadow ease-in-out 0.15s;
                        -o-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
                        transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
                        }
                        .form-control:focus {
                        border-color: #66afe9;
                        outline: 0;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
                            0 0 8px rgba(102, 175, 233, 0.6);
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
                            0 0 8px rgba(102, 175, 233, 0.6);
                        }
                        .form-control::-moz-placeholder {
                        color: #999;
                        opacity: 1;
                        }
                        .form-control:-ms-input-placeholder {
                        color: #999;
                        }
                        .form-control::-webkit-input-placeholder {
                        color: #999;
                        }
                        .form-control::-ms-expand {
                        background-color: transparent;
                        border: 0;
                        }
                        .form-control[disabled],
                        .form-control[readonly],
                        fieldset[disabled] .form-control {
                        background-color: #eee;
                        opacity: 1;
                        }
                        .form-control[disabled],
                        fieldset[disabled] .form-control {
                        cursor: not-allowed;
                        }
                        textarea.form-control {
                        height: auto;
                        }
                        input[type="search"] {
                        -webkit-appearance: none;
                        }
                        @media screen and (-webkit-min-device-pixel-ratio: 0) {
                        input[type="date"].form-control,
                        input[type="datetime-local"].form-control,
                        input[type="month"].form-control,
                        input[type="time"].form-control {
                            line-height: 34px;
                        }
                        .input-group-sm input[type="date"],
                        .input-group-sm input[type="datetime-local"],
                        .input-group-sm input[type="month"],
                        .input-group-sm input[type="time"],
                        input[type="date"].input-sm,
                        input[type="datetime-local"].input-sm,
                        input[type="month"].input-sm,
                        input[type="time"].input-sm {
                            line-height: 30px;
                        }
                        .input-group-lg input[type="date"],
                        .input-group-lg input[type="datetime-local"],
                        .input-group-lg input[type="month"],
                        .input-group-lg input[type="time"],
                        input[type="date"].input-lg,
                        input[type="datetime-local"].input-lg,
                        input[type="month"].input-lg,
                        input[type="time"].input-lg {
                            line-height: 46px;
                        }
                        }
                        .form-group {
                        margin-bottom: 15px;
                        }
                        .checkbox,
                        .radio {
                        position: relative;
                        display: block;
                        margin-top: 10px;
                        margin-bottom: 10px;
                        }
                        .checkbox label,
                        .radio label {
                        min-height: 20px;
                        padding-left: 20px;
                        margin-bottom: 0;
                        font-weight: 400;
                        cursor: pointer;
                        }
                        .checkbox input[type="checkbox"],
                        .checkbox-inline input[type="checkbox"],
                        .radio input[type="radio"],
                        .radio-inline input[type="radio"] {
                        position: absolute;
                        margin-left: -20px;
                        }
                        .checkbox + .checkbox,
                        .radio + .radio {
                        margin-top: -5px;
                        }
                        .checkbox-inline,
                        .radio-inline {
                        position: relative;
                        display: inline-block;
                        padding-left: 20px;
                        margin-bottom: 0;
                        font-weight: 400;
                        vertical-align: middle;
                        cursor: pointer;
                        }
                        .checkbox-inline + .checkbox-inline,
                        .radio-inline + .radio-inline {
                        margin-top: 0;
                        margin-left: 10px;
                        }
                        fieldset[disabled] input[type="checkbox"],
                        fieldset[disabled] input[type="radio"],
                        input[type="checkbox"].disabled,
                        input[type="checkbox"][disabled],
                        input[type="radio"].disabled,
                        input[type="radio"][disabled] {
                        cursor: not-allowed;
                        }
                        .checkbox-inline.disabled,
                        .radio-inline.disabled,
                        fieldset[disabled] .checkbox-inline,
                        fieldset[disabled] .radio-inline {
                        cursor: not-allowed;
                        }
                        .checkbox.disabled label,
                        .radio.disabled label,
                        fieldset[disabled] .checkbox label,
                        fieldset[disabled] .radio label {
                        cursor: not-allowed;
                        }
                        .form-control-static {
                        min-height: 34px;
                        padding-top: 7px;
                        padding-bottom: 7px;
                        margin-bottom: 0;
                        }
                        .form-control-static.input-lg,
                        .form-control-static.input-sm {
                        padding-right: 0;
                        padding-left: 0;
                        }
                        .input-sm {
                        height: 30px;
                        padding: 5px 10px;
                        font-size: 12px;
                        line-height: 1.5;
                        border-radius: 3px;
                        }
                        select.input-sm {
                        height: 30px;
                        line-height: 30px;
                        }
                        select[multiple].input-sm,
                        textarea.input-sm {
                        height: auto;
                        }
                        .form-group-sm .form-control {
                        height: 30px;
                        padding: 5px 10px;
                        font-size: 12px;
                        line-height: 1.5;
                        border-radius: 3px;
                        }
                        .form-group-sm select.form-control {
                        height: 30px;
                        line-height: 30px;
                        }
                        .form-group-sm select[multiple].form-control,
                        .form-group-sm textarea.form-control {
                        height: auto;
                        }
                        .form-group-sm .form-control-static {
                        height: 30px;
                        min-height: 32px;
                        padding: 6px 10px;
                        font-size: 12px;
                        line-height: 1.5;
                        }
                        .input-lg {
                        height: 46px;
                        padding: 10px 16px;
                        font-size: 18px;
                        line-height: 1.3333333;
                        border-radius: 6px;
                        }
                        select.input-lg {
                        height: 46px;
                        line-height: 46px;
                        }
                        select[multiple].input-lg,
                        textarea.input-lg {
                        height: auto;
                        }
                        .form-group-lg .form-control {
                        height: 46px;
                        padding: 10px 16px;
                        font-size: 18px;
                        line-height: 1.3333333;
                        border-radius: 6px;
                        }
                        .form-group-lg select.form-control {
                        height: 46px;
                        line-height: 46px;
                        }
                        .form-group-lg select[multiple].form-control,
                        .form-group-lg textarea.form-control {
                        height: auto;
                        }
                        .form-group-lg .form-control-static {
                        height: 46px;
                        min-height: 38px;
                        padding: 11px 16px;
                        font-size: 18px;
                        line-height: 1.3333333;
                        }
                        .has-feedback {
                        position: relative;
                        }
                        .has-feedback .form-control {
                        padding-right: 42.5px;
                        }
                        .form-control-feedback {
                        position: absolute;
                        top: 0;
                        right: 0;
                        z-index: 2;
                        display: block;
                        width: 34px;
                        height: 34px;
                        line-height: 34px;
                        text-align: center;
                        pointer-events: none;
                        }
                        .form-group-lg .form-control + .form-control-feedback,
                        .input-group-lg + .form-control-feedback,
                        .input-lg + .form-control-feedback {
                        width: 46px;
                        height: 46px;
                        line-height: 46px;
                        }
                        .form-group-sm .form-control + .form-control-feedback,
                        .input-group-sm + .form-control-feedback,
                        .input-sm + .form-control-feedback {
                        width: 30px;
                        height: 30px;
                        line-height: 30px;
                        }
                        .has-success .checkbox,
                        .has-success .checkbox-inline,
                        .has-success .control-label,
                        .has-success .help-block,
                        .has-success .radio,
                        .has-success .radio-inline,
                        .has-success.checkbox label,
                        .has-success.checkbox-inline label,
                        .has-success.radio label,
                        .has-success.radio-inline label {
                        color: #3c763d;
                        }
                        .has-success .form-control {
                        border-color: #3c763d;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                        }
                        .has-success .form-control:focus {
                        border-color: #2b542c;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #67b168;
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #67b168;
                        }
                        .has-success .input-group-addon {
                        color: #3c763d;
                        background-color: #dff0d8;
                        border-color: #3c763d;
                        }
                        .has-success .form-control-feedback {
                        color: #3c763d;
                        }
                        .has-warning .checkbox,
                        .has-warning .checkbox-inline,
                        .has-warning .control-label,
                        .has-warning .help-block,
                        .has-warning .radio,
                        .has-warning .radio-inline,
                        .has-warning.checkbox label,
                        .has-warning.checkbox-inline label,
                        .has-warning.radio label,
                        .has-warning.radio-inline label {
                        color: #8a6d3b;
                        }
                        .has-warning .form-control {
                        border-color: #8a6d3b;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                        }
                        .has-warning .form-control:focus {
                        border-color: #66512c;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c0a16b;
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #c0a16b;
                        }
                        .has-warning .input-group-addon {
                        color: #8a6d3b;
                        background-color: #fcf8e3;
                        border-color: #8a6d3b;
                        }
                        .has-warning .form-control-feedback {
                        color: #8a6d3b;
                        }
                        .has-error .checkbox,
                        .has-error .checkbox-inline,
                        .has-error .control-label,
                        .has-error .help-block,
                        .has-error .radio,
                        .has-error .radio-inline,
                        .has-error.checkbox label,
                        .has-error.checkbox-inline label,
                        .has-error.radio label,
                        .has-error.radio-inline label {
                        color: #a94442;
                        }
                        .has-error .form-control {
                        border-color: #a94442;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                        }
                        .has-error .form-control:focus {
                        border-color: #843534;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ce8483;
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #ce8483;
                        }
                        .has-error .input-group-addon {
                        color: #a94442;
                        background-color: #f2dede;
                        border-color: #a94442;
                        }
                        .has-error .form-control-feedback {
                        color: #a94442;
                        }
                        .has-feedback label ~ .form-control-feedback {
                        top: 25px;
                        }
                        .has-feedback label.sr-only ~ .form-control-feedback {
                        top: 0;
                        }
                        .help-block {
                        display: block;
                        margin-top: 5px;
                        margin-bottom: 10px;
                        color: #737373;
                        }
                        @media (min-width: 768px) {
                        .form-inline .form-group {
                            display: inline-block;
                            margin-bottom: 0;
                            vertical-align: middle;
                        }
                        .form-inline .form-control {
                            display: inline-block;
                            width: auto;
                            vertical-align: middle;
                        }
                        .form-inline .form-control-static {
                            display: inline-block;
                        }
                        .form-inline .input-group {
                            display: inline-table;
                            vertical-align: middle;
                        }
                        .form-inline .input-group .form-control,
                        .form-inline .input-group .input-group-addon,
                        .form-inline .input-group .input-group-btn {
                            width: auto;
                        }
                        .form-inline .input-group > .form-control {
                            width: 100%;
                        }
                        .form-inline .control-label {
                            margin-bottom: 0;
                            vertical-align: middle;
                        }
                        .form-inline .checkbox,
                        .form-inline .radio {
                            display: inline-block;
                            margin-top: 0;
                            margin-bottom: 0;
                            vertical-align: middle;
                        }
                        .form-inline .checkbox label,
                        .form-inline .radio label {
                            padding-left: 0;
                        }
                        .form-inline .checkbox input[type="checkbox"],
                        .form-inline .radio input[type="radio"] {
                            position: relative;
                            margin-left: 0;
                        }
                        .form-inline .has-feedback .form-control-feedback {
                            top: 0;
                        }
                        }
                        .form-horizontal .checkbox,
                        .form-horizontal .checkbox-inline,
                        .form-horizontal .radio,
                        .form-horizontal .radio-inline {
                        padding-top: 7px;
                        margin-top: 0;
                        margin-bottom: 0;
                        }
                        .form-horizontal .checkbox,
                        .form-horizontal .radio {
                        min-height: 27px;
                        }
                        .form-horizontal .form-group {
                        margin-right: -15px;
                        margin-left: -15px;
                        }
                        @media (min-width: 768px) {
                        .form-horizontal .control-label {
                            padding-top: 7px;
                            margin-bottom: 0;
                            text-align: right;
                        }
                        }
                        .form-horizontal .has-feedback .form-control-feedback {
                        right: 15px;
                        }
                        @media (min-width: 768px) {
                        .form-horizontal .form-group-lg .control-label {
                            padding-top: 11px;
                            font-size: 18px;
                        }
                        }
                        @media (min-width: 768px) {
                        .form-horizontal .form-group-sm .control-label {
                            padding-top: 6px;
                            font-size: 12px;
                        }
                        }
                        .btn {
                        display: inline-block;
                        padding: 6px 12px;
                        margin-bottom: 0;
                        font-size: 14px;
                        font-weight: 400;
                        line-height: 1.42857143;
                        text-align: center;
                        white-space: nowrap;
                        vertical-align: middle;
                        -ms-touch-action: manipulation;
                        touch-action: manipulation;
                        cursor: pointer;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        user-select: none;
                        background-image: none;
                        border: 1px solid transparent;
                        border-radius: 4px;
                        }
                        .btn.active.focus,
                        .btn.active:focus,
                        .btn.focus,
                        .btn:active.focus,
                        .btn:active:focus,
                        .btn:focus {
                        outline: 5px auto -webkit-focus-ring-color;
                        outline-offset: -2px;
                        }
                        .btn.focus,
                        .btn:focus,
                        .btn:hover {
                        color: #333;
                        text-decoration: none;
                        }
                        .btn.active,
                        .btn:active {
                        background-image: none;
                        outline: 0;
                        -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
                        box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
                        }
                        .btn.disabled,
                        .btn[disabled],
                        fieldset[disabled] .btn {
                        cursor: not-allowed;
                        -webkit-box-shadow: none;
                        box-shadow: none;
                        opacity: 0.65;
                        }
                        a.btn.disabled,
                        fieldset[disabled] a.btn {
                        pointer-events: none;
                        }
                        .btn-default {
                        color: #333;
                        background-color: #fff;
                        border-color: #ccc;
                        }
                        .btn-default.focus,
                        .btn-default:focus {
                        color: #333;
                        background-color: #e6e6e6;
                        border-color: #8c8c8c;
                        }
                        .btn-default:hover {
                        color: #333;
                        background-color: #e6e6e6;
                        border-color: #adadad;
                        }
                        .btn-default.active,
                        .btn-default:active,
                        .open > .dropdown-toggle.btn-default {
                        color: #333;
                        background-color: #e6e6e6;
                        border-color: #adadad;
                        }
                        .btn-default.active.focus,
                        .btn-default.active:focus,
                        .btn-default.active:hover,
                        .btn-default:active.focus,
                        .btn-default:active:focus,
                        .btn-default:active:hover,
                        .open > .dropdown-toggle.btn-default.focus,
                        .open > .dropdown-toggle.btn-default:focus,
                        .open > .dropdown-toggle.btn-default:hover {
                        color: #333;
                        background-color: #d4d4d4;
                        border-color: #8c8c8c;
                        }
                        .btn-default.active,
                        .btn-default:active,
                        .open > .dropdown-toggle.btn-default {
                        background-image: none;
                        }
                        .btn-default.disabled.focus,
                        .btn-default.disabled:focus,
                        .btn-default.disabled:hover,
                        .btn-default[disabled].focus,
                        .btn-default[disabled]:focus,
                        .btn-default[disabled]:hover,
                        fieldset[disabled] .btn-default.focus,
                        fieldset[disabled] .btn-default:focus,
                        fieldset[disabled] .btn-default:hover {
                        background-color: #fff;
                        border-color: #ccc;
                        }
                        .btn-default .badge {
                        color: #fff;
                        background-color: #333;
                        }
                        .btn-primary {
                        color: #fff;
                        background-color: #337ab7;
                        border-color: #2e6da4;
                        }
                        .btn-primary.focus,
                        .btn-primary:focus {
                        color: #fff;
                        background-color: #286090;
                        border-color: #122b40;
                        }
                        .btn-primary:hover {
                        color: #fff;
                        background-color: #286090;
                        border-color: #204d74;
                        }
                        .btn-primary.active,
                        .btn-primary:active,
                        .open > .dropdown-toggle.btn-primary {
                        color: #fff;
                        background-color: #286090;
                        border-color: #204d74;
                        }
                        .btn-primary.active.focus,
                        .btn-primary.active:focus,
                        .btn-primary.active:hover,
                        .btn-primary:active.focus,
                        .btn-primary:active:focus,
                        .btn-primary:active:hover,
                        .open > .dropdown-toggle.btn-primary.focus,
                        .open > .dropdown-toggle.btn-primary:focus,
                        .open > .dropdown-toggle.btn-primary:hover {
                        color: #fff;
                        background-color: #204d74;
                        border-color: #122b40;
                        }
                        .btn-primary.active,
                        .btn-primary:active,
                        .open > .dropdown-toggle.btn-primary {
                        background-image: none;
                        }
                        .btn-primary.disabled.focus,
                        .btn-primary.disabled:focus,
                        .btn-primary.disabled:hover,
                        .btn-primary[disabled].focus,
                        .btn-primary[disabled]:focus,
                        .btn-primary[disabled]:hover,
                        fieldset[disabled] .btn-primary.focus,
                        fieldset[disabled] .btn-primary:focus,
                        fieldset[disabled] .btn-primary:hover {
                        background-color: #337ab7;
                        border-color: #2e6da4;
                        }
                        .btn-primary .badge {
                        color: #337ab7;
                        background-color: #fff;
                        }
                        .btn-success {
                        color: #fff;
                        background-color: #5cb85c;
                        border-color: #4cae4c;
                        }
                        .btn-success.focus,
                        .btn-success:focus {
                        color: #fff;
                        background-color: #449d44;
                        border-color: #255625;
                        }
                        .btn-success:hover {
                        color: #fff;
                        background-color: #449d44;
                        border-color: #398439;
                        }
                        .btn-success.active,
                        .btn-success:active,
                        .open > .dropdown-toggle.btn-success {
                        color: #fff;
                        background-color: #449d44;
                        border-color: #398439;
                        }
                        .btn-success.active.focus,
                        .btn-success.active:focus,
                        .btn-success.active:hover,
                        .btn-success:active.focus,
                        .btn-success:active:focus,
                        .btn-success:active:hover,
                        .open > .dropdown-toggle.btn-success.focus,
                        .open > .dropdown-toggle.btn-success:focus,
                        .open > .dropdown-toggle.btn-success:hover {
                        color: #fff;
                        background-color: #398439;
                        border-color: #255625;
                        }
                        .btn-success.active,
                        .btn-success:active,
                        .open > .dropdown-toggle.btn-success {
                        background-image: none;
                        }
                        .btn-success.disabled.focus,
                        .btn-success.disabled:focus,
                        .btn-success.disabled:hover,
                        .btn-success[disabled].focus,
                        .btn-success[disabled]:focus,
                        .btn-success[disabled]:hover,
                        fieldset[disabled] .btn-success.focus,
                        fieldset[disabled] .btn-success:focus,
                        fieldset[disabled] .btn-success:hover {
                        background-color: #5cb85c;
                        border-color: #4cae4c;
                        }
                        .btn-success .badge {
                        color: #5cb85c;
                        background-color: #fff;
                        }
                        .btn-info {
                        color: #fff;
                        background-color: #5bc0de;
                        border-color: #46b8da;
                        }
                        .btn-info.focus,
                        .btn-info:focus {
                        color: #fff;
                        background-color: #31b0d5;
                        border-color: #1b6d85;
                        }
                        .btn-info:hover {
                        color: #fff;
                        background-color: #31b0d5;
                        border-color: #269abc;
                        }
                        .btn-info.active,
                        .btn-info:active,
                        .open > .dropdown-toggle.btn-info {
                        color: #fff;
                        background-color: #31b0d5;
                        border-color: #269abc;
                        }
                        .btn-info.active.focus,
                        .btn-info.active:focus,
                        .btn-info.active:hover,
                        .btn-info:active.focus,
                        .btn-info:active:focus,
                        .btn-info:active:hover,
                        .open > .dropdown-toggle.btn-info.focus,
                        .open > .dropdown-toggle.btn-info:focus,
                        .open > .dropdown-toggle.btn-info:hover {
                        color: #fff;
                        background-color: #269abc;
                        border-color: #1b6d85;
                        }
                        .btn-info.active,
                        .btn-info:active,
                        .open > .dropdown-toggle.btn-info {
                        background-image: none;
                        }
                        .btn-info.disabled.focus,
                        .btn-info.disabled:focus,
                        .btn-info.disabled:hover,
                        .btn-info[disabled].focus,
                        .btn-info[disabled]:focus,
                        .btn-info[disabled]:hover,
                        fieldset[disabled] .btn-info.focus,
                        fieldset[disabled] .btn-info:focus,
                        fieldset[disabled] .btn-info:hover {
                        background-color: #5bc0de;
                        border-color: #46b8da;
                        }
                        .btn-info .badge {
                        color: #5bc0de;
                        background-color: #fff;
                        }
                        .btn-warning {
                        color: #fff;
                        background-color: #f0ad4e;
                        border-color: #eea236;
                        }
                        .btn-warning.focus,
                        .btn-warning:focus {
                        color: #fff;
                        background-color: #ec971f;
                        border-color: #985f0d;
                        }
                        .btn-warning:hover {
                        color: #fff;
                        background-color: #ec971f;
                        border-color: #d58512;
                        }
                        .btn-warning.active,
                        .btn-warning:active,
                        .open > .dropdown-toggle.btn-warning {
                        color: #fff;
                        background-color: #ec971f;
                        border-color: #d58512;
                        }
                        .btn-warning.active.focus,
                        .btn-warning.active:focus,
                        .btn-warning.active:hover,
                        .btn-warning:active.focus,
                        .btn-warning:active:focus,
                        .btn-warning:active:hover,
                        .open > .dropdown-toggle.btn-warning.focus,
                        .open > .dropdown-toggle.btn-warning:focus,
                        .open > .dropdown-toggle.btn-warning:hover {
                        color: #fff;
                        background-color: #d58512;
                        border-color: #985f0d;
                        }
                        .btn-warning.active,
                        .btn-warning:active,
                        .open > .dropdown-toggle.btn-warning {
                        background-image: none;
                        }
                        .btn-warning.disabled.focus,
                        .btn-warning.disabled:focus,
                        .btn-warning.disabled:hover,
                        .btn-warning[disabled].focus,
                        .btn-warning[disabled]:focus,
                        .btn-warning[disabled]:hover,
                        fieldset[disabled] .btn-warning.focus,
                        fieldset[disabled] .btn-warning:focus,
                        fieldset[disabled] .btn-warning:hover {
                        background-color: #f0ad4e;
                        border-color: #eea236;
                        }
                        .btn-warning .badge {
                        color: #f0ad4e;
                        background-color: #fff;
                        }
                        .btn-danger {
                        color: #fff;
                        background-color: #d9534f;
                        border-color: #d43f3a;
                        }
                        .btn-danger.focus,
                        .btn-danger:focus {
                        color: #fff;
                        background-color: #c9302c;
                        border-color: #761c19;
                        }
                        .btn-danger:hover {
                        color: #fff;
                        background-color: #c9302c;
                        border-color: #ac2925;
                        }
                        .btn-danger.active,
                        .btn-danger:active,
                        .open > .dropdown-toggle.btn-danger {
                        color: #fff;
                        background-color: #c9302c;
                        border-color: #ac2925;
                        }
                        .btn-danger.active.focus,
                        .btn-danger.active:focus,
                        .btn-danger.active:hover,
                        .btn-danger:active.focus,
                        .btn-danger:active:focus,
                        .btn-danger:active:hover,
                        .open > .dropdown-toggle.btn-danger.focus,
                        .open > .dropdown-toggle.btn-danger:focus,
                        .open > .dropdown-toggle.btn-danger:hover {
                        color: #fff;
                        background-color: #ac2925;
                        border-color: #761c19;
                        }
                        .btn-danger.active,
                        .btn-danger:active,
                        .open > .dropdown-toggle.btn-danger {
                        background-image: none;
                        }
                        .btn-danger.disabled.focus,
                        .btn-danger.disabled:focus,
                        .btn-danger.disabled:hover,
                        .btn-danger[disabled].focus,
                        .btn-danger[disabled]:focus,
                        .btn-danger[disabled]:hover,
                        fieldset[disabled] .btn-danger.focus,
                        fieldset[disabled] .btn-danger:focus,
                        fieldset[disabled] .btn-danger:hover {
                        background-color: #d9534f;
                        border-color: #d43f3a;
                        }
                        .btn-danger .badge {
                        color: #d9534f;
                        background-color: #fff;
                        }
                        .btn-link {
                        font-weight: 400;
                        color: #337ab7;
                        border-radius: 0;
                        }
                        .btn-link,
                        .btn-link.active,
                        .btn-link:active,
                        .btn-link[disabled],
                        fieldset[disabled] .btn-link {
                        background-color: transparent;
                        -webkit-box-shadow: none;
                        box-shadow: none;
                        }
                        .btn-link,
                        .btn-link:active,
                        .btn-link:focus,
                        .btn-link:hover {
                        border-color: transparent;
                        }
                        .btn-link:focus,
                        .btn-link:hover {
                        color: #23527c;
                        text-decoration: underline;
                        background-color: transparent;
                        }
                        .btn-link[disabled]:focus,
                        .btn-link[disabled]:hover,
                        fieldset[disabled] .btn-link:focus,
                        fieldset[disabled] .btn-link:hover {
                        color: #777;
                        text-decoration: none;
                        }
                        .btn-group-lg > .btn,
                        .btn-lg {
                        padding: 10px 16px;
                        font-size: 18px;
                        line-height: 1.3333333;
                        border-radius: 6px;
                        }
                        .btn-group-sm > .btn,
                        .btn-sm {
                        padding: 5px 10px;
                        font-size: 12px;
                        line-height: 1.5;
                        border-radius: 3px;
                        }
                        .btn-group-xs > .btn,
                        .btn-xs {
                        padding: 1px 5px;
                        font-size: 12px;
                        line-height: 1.5;
                        border-radius: 3px;
                        }
                        .btn-block {
                        display: block;
                        width: 100%;
                        }
                        .btn-block + .btn-block {
                        margin-top: 5px;
                        }
                        input[type="button"].btn-block,
                        input[type="reset"].btn-block,
                        input[type="submit"].btn-block {
                        width: 100%;
                        }
                        .fade {
                        opacity: 0;
                        -webkit-transition: opacity 0.15s linear;
                        -o-transition: opacity 0.15s linear;
                        transition: opacity 0.15s linear;
                        }
                        .fade.in {
                        opacity: 1;
                        }
                        .collapse {
                        display: none;
                        }
                        .collapse.in {
                        display: block;
                        }
                        tr.collapse.in {
                        display: table-row;
                        }
                        tbody.collapse.in {
                        display: table-row-group;
                        }
                        .collapsing {
                        position: relative;
                        height: 0;
                        overflow: hidden;
                        -webkit-transition-timing-function: ease;
                        -o-transition-timing-function: ease;
                        transition-timing-function: ease;
                        -webkit-transition-duration: 0.35s;
                        -o-transition-duration: 0.35s;
                        transition-duration: 0.35s;
                        -webkit-transition-property: height, visibility;
                        -o-transition-property: height, visibility;
                        transition-property: height, visibility;
                        }
                        .caret {
                        display: inline-block;
                        width: 0;
                        height: 0;
                        margin-left: 2px;
                        vertical-align: middle;
                        border-top: 4px dashed;
                        border-right: 4px solid transparent;
                        border-left: 4px solid transparent;
                        }
                        .dropdown,
                        .dropup {
                        position: relative;
                        }
                        .dropdown-toggle:focus {
                        outline: 0;
                        }
                        .dropdown-menu {
                        position: absolute;
                        top: 100%;
                        left: 0;
                        z-index: 1000;
                        display: none;
                        float: left;
                        min-width: 160px;
                        padding: 5px 0;
                        margin: 2px 0 0;
                        font-size: 14px;
                        text-align: left;
                        list-style: none;
                        background-color: #fff;
                        -webkit-background-clip: padding-box;
                        background-clip: padding-box;
                        border: 1px solid #ccc;
                        border: 1px solid rgba(0, 0, 0, 0.15);
                        border-radius: 4px;
                        -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
                        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
                        }
                        .dropdown-menu.pull-right {
                        right: 0;
                        left: auto;
                        }
                        .dropdown-menu .divider {
                        height: 1px;
                        margin: 9px 0;
                        overflow: hidden;
                        background-color: #e5e5e5;
                        }
                        .dropdown-menu > li > a {
                        display: block;
                        padding: 3px 20px;
                        clear: both;
                        font-weight: 400;
                        line-height: 1.42857143;
                        color: #333;
                        white-space: nowrap;
                        }
                        .dropdown-menu > li > a:focus,
                        .dropdown-menu > li > a:hover {
                        color: #262626;
                        text-decoration: none;
                        background-color: #f5f5f5;
                        }
                        .dropdown-menu > .active > a,
                        .dropdown-menu > .active > a:focus,
                        .dropdown-menu > .active > a:hover {
                        color: #fff;
                        text-decoration: none;
                        background-color: #337ab7;
                        outline: 0;
                        }
                        .dropdown-menu > .disabled > a,
                        .dropdown-menu > .disabled > a:focus,
                        .dropdown-menu > .disabled > a:hover {
                        color: #777;
                        }
                        .dropdown-menu > .disabled > a:focus,
                        .dropdown-menu > .disabled > a:hover {
                        text-decoration: none;
                        cursor: not-allowed;
                        background-color: transparent;
                        background-image: none;
                        }
                        .open > .dropdown-menu {
                        display: block;
                        }
                        .open > a {
                        outline: 0;
                        }
                        .dropdown-menu-right {
                        right: 0;
                        left: auto;
                        }
                        .dropdown-menu-left {
                        right: auto;
                        left: 0;
                        }
                        .dropdown-header {
                        display: block;
                        padding: 3px 20px;
                        font-size: 12px;
                        line-height: 1.42857143;
                        color: #777;
                        white-space: nowrap;
                        }
                        .dropdown-backdrop {
                        position: fixed;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        left: 0;
                        z-index: 990;
                        }
                        .pull-right > .dropdown-menu {
                        right: 0;
                        left: auto;
                        }
                        .dropup .caret,
                        .navbar-fixed-bottom .dropdown .caret {
                        content: "";
                        border-top: 0;
                        border-bottom: 4px dashed;
                        }
                        .dropup .dropdown-menu,
                        .navbar-fixed-bottom .dropdown .dropdown-menu {
                        top: auto;
                        bottom: 100%;
                        margin-bottom: 2px;
                        }
                        @media (min-width: 768px) {
                        .navbar-right .dropdown-menu {
                            right: 0;
                            left: auto;
                        }
                        .navbar-right .dropdown-menu-left {
                            right: auto;
                            left: 0;
                        }
                        }
                        .btn-group,
                        .btn-group-vertical {
                        position: relative;
                        display: inline-block;
                        vertical-align: middle;
                        }
                        .btn-group-vertical > .btn,
                        .btn-group > .btn {
                        position: relative;
                        float: left;
                        }
                        .btn-group-vertical > .btn.active,
                        .btn-group-vertical > .btn:active,
                        .btn-group-vertical > .btn:focus,
                        .btn-group-vertical > .btn:hover,
                        .btn-group > .btn.active,
                        .btn-group > .btn:active,
                        .btn-group > .btn:focus,
                        .btn-group > .btn:hover {
                        z-index: 2;
                        }
                        .btn-group .btn + .btn,
                        .btn-group .btn + .btn-group,
                        .btn-group .btn-group + .btn,
                        .btn-group .btn-group + .btn-group {
                        margin-left: -1px;
                        }
                        .btn-toolbar {
                        margin-left: -5px;
                        }
                        .btn-toolbar .btn,
                        .btn-toolbar .btn-group,
                        .btn-toolbar .input-group {
                        float: left;
                        }
                        .btn-toolbar > .btn,
                        .btn-toolbar > .btn-group,
                        .btn-toolbar > .input-group {
                        margin-left: 5px;
                        }
                        .btn-group > .btn:not(:first-child):not(:last-child):not(.dropdown-toggle) {
                        border-radius: 0;
                        }
                        .btn-group > .btn:first-child {
                        margin-left: 0;
                        }
                        .btn-group > .btn:first-child:not(:last-child):not(.dropdown-toggle) {
                        border-top-right-radius: 0;
                        border-bottom-right-radius: 0;
                        }
                        .btn-group > .btn:last-child:not(:first-child),
                        .btn-group > .dropdown-toggle:not(:first-child) {
                        border-top-left-radius: 0;
                        border-bottom-left-radius: 0;
                        }
                        .btn-group > .btn-group {
                        float: left;
                        }
                        .btn-group > .btn-group:not(:first-child):not(:last-child) > .btn {
                        border-radius: 0;
                        }
                        .btn-group > .btn-group:first-child:not(:last-child) > .btn:last-child,
                        .btn-group > .btn-group:first-child:not(:last-child) > .dropdown-toggle {
                        border-top-right-radius: 0;
                        border-bottom-right-radius: 0;
                        }
                        .btn-group > .btn-group:last-child:not(:first-child) > .btn:first-child {
                        border-top-left-radius: 0;
                        border-bottom-left-radius: 0;
                        }
                        .btn-group .dropdown-toggle:active,
                        .btn-group.open .dropdown-toggle {
                        outline: 0;
                        }
                        .btn-group > .btn + .dropdown-toggle {
                        padding-right: 8px;
                        padding-left: 8px;
                        }
                        .btn-group > .btn-lg + .dropdown-toggle {
                        padding-right: 12px;
                        padding-left: 12px;
                        }
                        .btn-group.open .dropdown-toggle {
                        -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
                        box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
                        }
                        .btn-group.open .dropdown-toggle.btn-link {
                        -webkit-box-shadow: none;
                        box-shadow: none;
                        }
                        .btn .caret {
                        margin-left: 0;
                        }
                        .btn-lg .caret {
                        border-width: 5px 5px 0;
                        border-bottom-width: 0;
                        }
                        .dropup .btn-lg .caret {
                        border-width: 0 5px 5px;
                        }
                        .btn-group-vertical > .btn,
                        .btn-group-vertical > .btn-group,
                        .btn-group-vertical > .btn-group > .btn {
                        display: block;
                        float: none;
                        width: 100%;
                        max-width: 100%;
                        }
                        .btn-group-vertical > .btn-group > .btn {
                        float: none;
                        }
                        .btn-group-vertical > .btn + .btn,
                        .btn-group-vertical > .btn + .btn-group,
                        .btn-group-vertical > .btn-group + .btn,
                        .btn-group-vertical > .btn-group + .btn-group {
                        margin-top: -1px;
                        margin-left: 0;
                        }
                        .btn-group-vertical > .btn:not(:first-child):not(:last-child) {
                        border-radius: 0;
                        }
                        .btn-group-vertical > .btn:first-child:not(:last-child) {
                        border-top-left-radius: 4px;
                        border-top-right-radius: 4px;
                        border-bottom-right-radius: 0;
                        border-bottom-left-radius: 0;
                        }
                        .btn-group-vertical > .btn:last-child:not(:first-child) {
                        border-top-left-radius: 0;
                        border-top-right-radius: 0;
                        border-bottom-right-radius: 4px;
                        border-bottom-left-radius: 4px;
                        }
                        .btn-group-vertical > .btn-group:not(:first-child):not(:last-child) > .btn {
                        border-radius: 0;
                        }
                        .btn-group-vertical > .btn-group:first-child:not(:last-child) > .btn:last-child,
                        .btn-group-vertical
                        > .btn-group:first-child:not(:last-child)
                        > .dropdown-toggle {
                        border-bottom-right-radius: 0;
                        border-bottom-left-radius: 0;
                        }
                        .btn-group-vertical
                        > .btn-group:last-child:not(:first-child)
                        > .btn:first-child {
                        border-top-left-radius: 0;
                        border-top-right-radius: 0;
                        }
                        .btn-group-justified {
                        display: table;
                        width: 100%;
                        table-layout: fixed;
                        border-collapse: separate;
                        }
                        .btn-group-justified > .btn,
                        .btn-group-justified > .btn-group {
                        display: table-cell;
                        float: none;
                        width: 1%;
                        }
                        .btn-group-justified > .btn-group .btn {
                        width: 100%;
                        }
                        .btn-group-justified > .btn-group .dropdown-menu {
                        left: auto;
                        }
                        [data-toggle="buttons"] > .btn input[type="checkbox"],
                        [data-toggle="buttons"] > .btn input[type="radio"],
                        [data-toggle="buttons"] > .btn-group > .btn input[type="checkbox"],
                        [data-toggle="buttons"] > .btn-group > .btn input[type="radio"] {
                        position: absolute;
                        clip: rect(0, 0, 0, 0);
                        pointer-events: none;
                        }
                        .input-group {
                        position: relative;
                        display: table;
                        border-collapse: separate;
                        }
                        .input-group[class*="col-"] {
                        float: none;
                        padding-right: 0;
                        padding-left: 0;
                        }
                        .input-group .form-control {
                        position: relative;
                        z-index: 2;
                        float: left;
                        width: 100%;
                        margin-bottom: 0;
                        }
                        .input-group .form-control:focus {
                        z-index: 3;
                        }
                        .input-group-lg > .form-control,
                        .input-group-lg > .input-group-addon,
                        .input-group-lg > .input-group-btn > .btn {
                        height: 46px;
                        padding: 10px 16px;
                        font-size: 18px;
                        line-height: 1.3333333;
                        border-radius: 6px;
                        }
                        select.input-group-lg > .form-control,
                        select.input-group-lg > .input-group-addon,
                        select.input-group-lg > .input-group-btn > .btn {
                        height: 46px;
                        line-height: 46px;
                        }
                        select[multiple].input-group-lg > .form-control,
                        select[multiple].input-group-lg > .input-group-addon,
                        select[multiple].input-group-lg > .input-group-btn > .btn,
                        textarea.input-group-lg > .form-control,
                        textarea.input-group-lg > .input-group-addon,
                        textarea.input-group-lg > .input-group-btn > .btn {
                        height: auto;
                        }
                        .input-group-sm > .form-control,
                        .input-group-sm > .input-group-addon,
                        .input-group-sm > .input-group-btn > .btn {
                        height: 30px;
                        padding: 5px 10px;
                        font-size: 12px;
                        line-height: 1.5;
                        border-radius: 3px;
                        }
                        select.input-group-sm > .form-control,
                        select.input-group-sm > .input-group-addon,
                        select.input-group-sm > .input-group-btn > .btn {
                        height: 30px;
                        line-height: 30px;
                        }
                        select[multiple].input-group-sm > .form-control,
                        select[multiple].input-group-sm > .input-group-addon,
                        select[multiple].input-group-sm > .input-group-btn > .btn,
                        textarea.input-group-sm > .form-control,
                        textarea.input-group-sm > .input-group-addon,
                        textarea.input-group-sm > .input-group-btn > .btn {
                        height: auto;
                        }
                        .input-group .form-control,
                        .input-group-addon,
                        .input-group-btn {
                        display: table-cell;
                        }
                        .input-group .form-control:not(:first-child):not(:last-child),
                        .input-group-addon:not(:first-child):not(:last-child),
                        .input-group-btn:not(:first-child):not(:last-child) {
                        border-radius: 0;
                        }
                        .input-group-addon,
                        .input-group-btn {
                        width: 1%;
                        white-space: nowrap;
                        vertical-align: middle;
                        }
                        .input-group-addon {
                        padding: 6px 12px;
                        font-size: 14px;
                        font-weight: 400;
                        line-height: 1;
                        color: #555;
                        text-align: center;
                        background-color: #eee;
                        border: 1px solid #ccc;
                        border-radius: 4px;
                        }
                        .input-group-addon.input-sm {
                        padding: 5px 10px;
                        font-size: 12px;
                        border-radius: 3px;
                        }
                        .input-group-addon.input-lg {
                        padding: 10px 16px;
                        font-size: 18px;
                        border-radius: 6px;
                        }
                        .input-group-addon input[type="checkbox"],
                        .input-group-addon input[type="radio"] {
                        margin-top: 0;
                        }
                        .input-group .form-control:first-child,
                        .input-group-addon:first-child,
                        .input-group-btn:first-child > .btn,
                        .input-group-btn:first-child > .btn-group > .btn,
                        .input-group-btn:first-child > .dropdown-toggle,
                        .input-group-btn:last-child > .btn-group:not(:last-child) > .btn,
                        .input-group-btn:last-child > .btn:not(:last-child):not(.dropdown-toggle) {
                        border-top-right-radius: 0;
                        border-bottom-right-radius: 0;
                        }
                        .input-group-addon:first-child {
                        border-right: 0;
                        }
                        .input-group .form-control:last-child,
                        .input-group-addon:last-child,
                        .input-group-btn:first-child > .btn-group:not(:first-child) > .btn,
                        .input-group-btn:first-child > .btn:not(:first-child),
                        .input-group-btn:last-child > .btn,
                        .input-group-btn:last-child > .btn-group > .btn,
                        .input-group-btn:last-child > .dropdown-toggle {
                        border-top-left-radius: 0;
                        border-bottom-left-radius: 0;
                        }
                        .input-group-addon:last-child {
                        border-left: 0;
                        }
                        .input-group-btn {
                        position: relative;
                        font-size: 0;
                        white-space: nowrap;
                        }
                        .input-group-btn > .btn {
                        position: relative;
                        }
                        .input-group-btn > .btn + .btn {
                        margin-left: -1px;
                        }
                        .input-group-btn > .btn:active,
                        .input-group-btn > .btn:focus,
                        .input-group-btn > .btn:hover {
                        z-index: 2;
                        }
                        .input-group-btn:first-child > .btn,
                        .input-group-btn:first-child > .btn-group {
                        margin-right: -1px;
                        }
                        .input-group-btn:last-child > .btn,
                        .input-group-btn:last-child > .btn-group {
                        z-index: 2;
                        margin-left: -1px;
                        }
                        .nav {
                        padding-left: 0;
                        margin-bottom: 0;
                        list-style: none;
                        }
                        .nav > li {
                        position: relative;
                        display: block;
                        }
                        .nav > li > a {
                        position: relative;
                        display: block;
                        padding: 10px 15px;
                        }
                        .nav > li > a:focus,
                        .nav > li > a:hover {
                        text-decoration: none;
                        background-color: #eee;
                        }
                        .nav > li.disabled > a {
                        color: #777;
                        }
                        .nav > li.disabled > a:focus,
                        .nav > li.disabled > a:hover {
                        color: #777;
                        text-decoration: none;
                        cursor: not-allowed;
                        background-color: transparent;
                        }
                        .nav .open > a,
                        .nav .open > a:focus,
                        .nav .open > a:hover {
                        background-color: #eee;
                        border-color: #337ab7;
                        }
                        .nav .nav-divider {
                        height: 1px;
                        margin: 9px 0;
                        overflow: hidden;
                        background-color: #e5e5e5;
                        }
                        .nav > li > a > img {
                        max-width: none;
                        }
                        .nav-tabs {
                        border-bottom: 1px solid #ddd;
                        }
                        .nav-tabs > li {
                        float: left;
                        margin-bottom: -1px;
                        }
                        .nav-tabs > li > a {
                        margin-right: 2px;
                        line-height: 1.42857143;
                        border: 1px solid transparent;
                        border-radius: 4px 4px 0 0;
                        }
                        .nav-tabs > li > a:hover {
                        border-color: #eee #eee #ddd;
                        }
                        .nav-tabs > li.active > a,
                        .nav-tabs > li.active > a:focus,
                        .nav-tabs > li.active > a:hover {
                        color: #555;
                        cursor: default;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        border-bottom-color: transparent;
                        }
                        .nav-tabs.nav-justified {
                        width: 100%;
                        border-bottom: 0;
                        }
                        .nav-tabs.nav-justified > li {
                        float: none;
                        }
                        .nav-tabs.nav-justified > li > a {
                        margin-bottom: 5px;
                        text-align: center;
                        }
                        .nav-tabs.nav-justified > .dropdown .dropdown-menu {
                        top: auto;
                        left: auto;
                        }
                        @media (min-width: 768px) {
                        .nav-tabs.nav-justified > li {
                            display: table-cell;
                            width: 1%;
                        }
                        .nav-tabs.nav-justified > li > a {
                            margin-bottom: 0;
                        }
                        }
                        .nav-tabs.nav-justified > li > a {
                        margin-right: 0;
                        border-radius: 4px;
                        }
                        .nav-tabs.nav-justified > .active > a,
                        .nav-tabs.nav-justified > .active > a:focus,
                        .nav-tabs.nav-justified > .active > a:hover {
                        border: 1px solid #ddd;
                        }
                        @media (min-width: 768px) {
                        .nav-tabs.nav-justified > li > a {
                            border-bottom: 1px solid #ddd;
                            border-radius: 4px 4px 0 0;
                        }
                        .nav-tabs.nav-justified > .active > a,
                        .nav-tabs.nav-justified > .active > a:focus,
                        .nav-tabs.nav-justified > .active > a:hover {
                            border-bottom-color: #fff;
                        }
                        }
                        .nav-pills > li {
                        float: left;
                        }
                        .nav-pills > li > a {
                        border-radius: 4px;
                        }
                        .nav-pills > li + li {
                        margin-left: 2px;
                        }
                        .nav-pills > li.active > a,
                        .nav-pills > li.active > a:focus,
                        .nav-pills > li.active > a:hover {
                        color: #fff;
                        background-color: #337ab7;
                        }
                        .nav-stacked > li {
                        float: none;
                        }
                        .nav-stacked > li + li {
                        margin-top: 2px;
                        margin-left: 0;
                        }
                        .nav-justified {
                        width: 100%;
                        }
                        .nav-justified > li {
                        float: none;
                        }
                        .nav-justified > li > a {
                        margin-bottom: 5px;
                        text-align: center;
                        }
                        .nav-justified > .dropdown .dropdown-menu {
                        top: auto;
                        left: auto;
                        }
                        @media (min-width: 768px) {
                        .nav-justified > li {
                            display: table-cell;
                            width: 1%;
                        }
                        .nav-justified > li > a {
                            margin-bottom: 0;
                        }
                        }
                        .nav-tabs-justified {
                        border-bottom: 0;
                        }
                        .nav-tabs-justified > li > a {
                        margin-right: 0;
                        border-radius: 4px;
                        }
                        .nav-tabs-justified > .active > a,
                        .nav-tabs-justified > .active > a:focus,
                        .nav-tabs-justified > .active > a:hover {
                        border: 1px solid #ddd;
                        }
                        @media (min-width: 768px) {
                        .nav-tabs-justified > li > a {
                            border-bottom: 1px solid #ddd;
                            border-radius: 4px 4px 0 0;
                        }
                        .nav-tabs-justified > .active > a,
                        .nav-tabs-justified > .active > a:focus,
                        .nav-tabs-justified > .active > a:hover {
                            border-bottom-color: #fff;
                        }
                        }
                        .tab-content > .tab-pane {
                        display: none;
                        }
                        .tab-content > .active {
                        display: block;
                        }
                        .nav-tabs .dropdown-menu {
                        margin-top: -1px;
                        border-top-left-radius: 0;
                        border-top-right-radius: 0;
                        }
                        .navbar {
                        position: relative;
                        min-height: 50px;
                        margin-bottom: 20px;
                        border: 1px solid transparent;
                        }
                        @media (min-width: 768px) {
                        .navbar {
                            border-radius: 4px;
                        }
                        }
                        @media (min-width: 768px) {
                        .navbar-header {
                            float: left;
                        }
                        }
                        .navbar-collapse {
                        padding-right: 15px;
                        padding-left: 15px;
                        overflow-x: visible;
                        -webkit-overflow-scrolling: touch;
                        border-top: 1px solid transparent;
                        -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
                        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
                        }
                        .navbar-collapse.in {
                        overflow-y: auto;
                        }
                        @media (min-width: 768px) {
                        .navbar-collapse {
                            width: auto;
                            border-top: 0;
                            -webkit-box-shadow: none;
                            box-shadow: none;
                        }
                        .navbar-collapse.collapse {
                            display: block !important;
                            height: auto !important;
                            padding-bottom: 0;
                            overflow: visible !important;
                        }
                        .navbar-collapse.in {
                            overflow-y: visible;
                        }
                        .navbar-fixed-bottom .navbar-collapse,
                        .navbar-fixed-top .navbar-collapse,
                        .navbar-static-top .navbar-collapse {
                            padding-right: 0;
                            padding-left: 0;
                        }
                        }
                        .navbar-fixed-bottom .navbar-collapse,
                        .navbar-fixed-top .navbar-collapse {
                        max-height: 340px;
                        }
                        @media (max-device-width: 480px) and (orientation: landscape) {
                        .navbar-fixed-bottom .navbar-collapse,
                        .navbar-fixed-top .navbar-collapse {
                            max-height: 200px;
                        }
                        }
                        .container-fluid > .navbar-collapse,
                        .container-fluid > .navbar-header,
                        .container > .navbar-collapse,
                        .container > .navbar-header {
                        margin-right: -15px;
                        margin-left: -15px;
                        }
                        @media (min-width: 768px) {
                        .container-fluid > .navbar-collapse,
                        .container-fluid > .navbar-header,
                        .container > .navbar-collapse,
                        .container > .navbar-header {
                            margin-right: 0;
                            margin-left: 0;
                        }
                        }
                        .navbar-static-top {
                        z-index: 1000;
                        border-width: 0 0 1px;
                        }
                        @media (min-width: 768px) {
                        .navbar-static-top {
                            border-radius: 0;
                        }
                        }
                        .navbar-fixed-bottom,
                        .navbar-fixed-top {
                        position: fixed;
                        right: 0;
                        left: 0;
                        z-index: 1030;
                        }
                        @media (min-width: 768px) {
                        .navbar-fixed-bottom,
                        .navbar-fixed-top {
                            border-radius: 0;
                        }
                        }
                        .navbar-fixed-top {
                        top: 0;
                        border-width: 0 0 1px;
                        }
                        .navbar-fixed-bottom {
                        bottom: 0;
                        margin-bottom: 0;
                        border-width: 1px 0 0;
                        }
                        .navbar-brand {
                        float: left;
                        height: 50px;
                        padding: 15px 15px;
                        font-size: 18px;
                        line-height: 20px;
                        }
                        .navbar-brand:focus,
                        .navbar-brand:hover {
                        text-decoration: none;
                        }
                        .navbar-brand > img {
                        display: block;
                        }
                        @media (min-width: 768px) {
                        .navbar > .container .navbar-brand,
                        .navbar > .container-fluid .navbar-brand {
                            margin-left: -15px;
                        }
                        }
                        .navbar-toggle {
                        position: relative;
                        float: right;
                        padding: 9px 10px;
                        margin-top: 8px;
                        margin-right: 15px;
                        margin-bottom: 8px;
                        background-color: transparent;
                        background-image: none;
                        border: 1px solid transparent;
                        border-radius: 4px;
                        }
                        .navbar-toggle:focus {
                        outline: 0;
                        }
                        .navbar-toggle .icon-bar {
                        display: block;
                        width: 22px;
                        height: 2px;
                        border-radius: 1px;
                        }
                        .navbar-toggle .icon-bar + .icon-bar {
                        margin-top: 4px;
                        }
                        @media (min-width: 768px) {
                        .navbar-toggle {
                            display: none;
                        }
                        }
                        .navbar-nav {
                        margin: 7.5px -15px;
                        }
                        .navbar-nav > li > a {
                        padding-top: 10px;
                        padding-bottom: 10px;
                        line-height: 20px;
                        }
                        @media (max-width: 767px) {
                        .navbar-nav .open .dropdown-menu {
                            position: static;
                            float: none;
                            width: auto;
                            margin-top: 0;
                            background-color: transparent;
                            border: 0;
                            -webkit-box-shadow: none;
                            box-shadow: none;
                        }
                        .navbar-nav .open .dropdown-menu .dropdown-header,
                        .navbar-nav .open .dropdown-menu > li > a {
                            padding: 5px 15px 5px 25px;
                        }
                        .navbar-nav .open .dropdown-menu > li > a {
                            line-height: 20px;
                        }
                        .navbar-nav .open .dropdown-menu > li > a:focus,
                        .navbar-nav .open .dropdown-menu > li > a:hover {
                            background-image: none;
                        }
                        }
                        @media (min-width: 768px) {
                        .navbar-nav {
                            float: left;
                            margin: 0;
                        }
                        .navbar-nav > li {
                            float: left;
                        }
                        .navbar-nav > li > a {
                            padding-top: 15px;
                            padding-bottom: 15px;
                        }
                        }
                        .navbar-form {
                        padding: 10px 15px;
                        margin-top: 8px;
                        margin-right: -15px;
                        margin-bottom: 8px;
                        margin-left: -15px;
                        border-top: 1px solid transparent;
                        border-bottom: 1px solid transparent;
                        -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1),
                            0 1px 0 rgba(255, 255, 255, 0.1);
                        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1),
                            0 1px 0 rgba(255, 255, 255, 0.1);
                        }
                        @media (min-width: 768px) {
                        .navbar-form .form-group {
                            display: inline-block;
                            margin-bottom: 0;
                            vertical-align: middle;
                        }
                        .navbar-form .form-control {
                            display: inline-block;
                            width: auto;
                            vertical-align: middle;
                        }
                        .navbar-form .form-control-static {
                            display: inline-block;
                        }
                        .navbar-form .input-group {
                            display: inline-table;
                            vertical-align: middle;
                        }
                        .navbar-form .input-group .form-control,
                        .navbar-form .input-group .input-group-addon,
                        .navbar-form .input-group .input-group-btn {
                            width: auto;
                        }
                        .navbar-form .input-group > .form-control {
                            width: 100%;
                        }
                        .navbar-form .control-label {
                            margin-bottom: 0;
                            vertical-align: middle;
                        }
                        .navbar-form .checkbox,
                        .navbar-form .radio {
                            display: inline-block;
                            margin-top: 0;
                            margin-bottom: 0;
                            vertical-align: middle;
                        }
                        .navbar-form .checkbox label,
                        .navbar-form .radio label {
                            padding-left: 0;
                        }
                        .navbar-form .checkbox input[type="checkbox"],
                        .navbar-form .radio input[type="radio"] {
                            position: relative;
                            margin-left: 0;
                        }
                        .navbar-form .has-feedback .form-control-feedback {
                            top: 0;
                        }
                        }
                        @media (max-width: 767px) {
                        .navbar-form .form-group {
                            margin-bottom: 5px;
                        }
                        .navbar-form .form-group:last-child {
                            margin-bottom: 0;
                        }
                        }
                        @media (min-width: 768px) {
                        .navbar-form {
                            width: auto;
                            padding-top: 0;
                            padding-bottom: 0;
                            margin-right: 0;
                            margin-left: 0;
                            border: 0;
                            -webkit-box-shadow: none;
                            box-shadow: none;
                        }
                        }
                        .navbar-nav > li > .dropdown-menu {
                        margin-top: 0;
                        border-top-left-radius: 0;
                        border-top-right-radius: 0;
                        }
                        .navbar-fixed-bottom .navbar-nav > li > .dropdown-menu {
                        margin-bottom: 0;
                        border-top-left-radius: 4px;
                        border-top-right-radius: 4px;
                        border-bottom-right-radius: 0;
                        border-bottom-left-radius: 0;
                        }
                        .navbar-btn {
                        margin-top: 8px;
                        margin-bottom: 8px;
                        }
                        .navbar-btn.btn-sm {
                        margin-top: 10px;
                        margin-bottom: 10px;
                        }
                        .navbar-btn.btn-xs {
                        margin-top: 14px;
                        margin-bottom: 14px;
                        }
                        .navbar-text {
                        margin-top: 15px;
                        margin-bottom: 15px;
                        }
                        @media (min-width: 768px) {
                        .navbar-text {
                            float: left;
                            margin-right: 15px;
                            margin-left: 15px;
                        }
                        }
                        @media (min-width: 768px) {
                        .navbar-left {
                            float: left !important;
                        }
                        .navbar-right {
                            float: right !important;
                            margin-right: -15px;
                        }
                        .navbar-right ~ .navbar-right {
                            margin-right: 0;
                        }
                        }
                        .navbar-default {
                        background-color: #f8f8f8;
                        border-color: #e7e7e7;
                        }
                        .navbar-default .navbar-brand {
                        color: #777;
                        }
                        .navbar-default .navbar-brand:focus,
                        .navbar-default .navbar-brand:hover {
                        color: #5e5e5e;
                        background-color: transparent;
                        }
                        .navbar-default .navbar-text {
                        color: #777;
                        }
                        .navbar-default .navbar-nav > li > a {
                        color: #777;
                        }
                        .navbar-default .navbar-nav > li > a:focus,
                        .navbar-default .navbar-nav > li > a:hover {
                        color: #333;
                        background-color: transparent;
                        }
                        .navbar-default .navbar-nav > .active > a,
                        .navbar-default .navbar-nav > .active > a:focus,
                        .navbar-default .navbar-nav > .active > a:hover {
                        color: #555;
                        background-color: #e7e7e7;
                        }
                        .navbar-default .navbar-nav > .disabled > a,
                        .navbar-default .navbar-nav > .disabled > a:focus,
                        .navbar-default .navbar-nav > .disabled > a:hover {
                        color: #ccc;
                        background-color: transparent;
                        }
                        .navbar-default .navbar-toggle {
                        border-color: #ddd;
                        }
                        .navbar-default .navbar-toggle:focus,
                        .navbar-default .navbar-toggle:hover {
                        background-color: #ddd;
                        }
                        .navbar-default .navbar-toggle .icon-bar {
                        background-color: #888;
                        }
                        .navbar-default .navbar-collapse,
                        .navbar-default .navbar-form {
                        border-color: #e7e7e7;
                        }
                        .navbar-default .navbar-nav > .open > a,
                        .navbar-default .navbar-nav > .open > a:focus,
                        .navbar-default .navbar-nav > .open > a:hover {
                        color: #555;
                        background-color: #e7e7e7;
                        }
                        @media (max-width: 767px) {
                        .navbar-default .navbar-nav .open .dropdown-menu > li > a {
                            color: #777;
                        }
                        .navbar-default .navbar-nav .open .dropdown-menu > li > a:focus,
                        .navbar-default .navbar-nav .open .dropdown-menu > li > a:hover {
                            color: #333;
                            background-color: transparent;
                        }
                        .navbar-default .navbar-nav .open .dropdown-menu > .active > a,
                        .navbar-default .navbar-nav .open .dropdown-menu > .active > a:focus,
                        .navbar-default .navbar-nav .open .dropdown-menu > .active > a:hover {
                            color: #555;
                            background-color: #e7e7e7;
                        }
                        .navbar-default .navbar-nav .open .dropdown-menu > .disabled > a,
                        .navbar-default .navbar-nav .open .dropdown-menu > .disabled > a:focus,
                        .navbar-default .navbar-nav .open .dropdown-menu > .disabled > a:hover {
                            color: #ccc;
                            background-color: transparent;
                        }
                        }
                        .navbar-default .navbar-link {
                        color: #777;
                        }
                        .navbar-default .navbar-link:hover {
                        color: #333;
                        }
                        .navbar-default .btn-link {
                        color: #777;
                        }
                        .navbar-default .btn-link:focus,
                        .navbar-default .btn-link:hover {
                        color: #333;
                        }
                        .navbar-default .btn-link[disabled]:focus,
                        .navbar-default .btn-link[disabled]:hover,
                        fieldset[disabled] .navbar-default .btn-link:focus,
                        fieldset[disabled] .navbar-default .btn-link:hover {
                        color: #ccc;
                        }
                        .navbar-inverse {
                        background-color: #222;
                        border-color: #080808;
                        }
                        .navbar-inverse .navbar-brand {
                        color: #9d9d9d;
                        }
                        .navbar-inverse .navbar-brand:focus,
                        .navbar-inverse .navbar-brand:hover {
                        color: #fff;
                        background-color: transparent;
                        }
                        .navbar-inverse .navbar-text {
                        color: #9d9d9d;
                        }
                        .navbar-inverse .navbar-nav > li > a {
                        color: #9d9d9d;
                        }
                        .navbar-inverse .navbar-nav > li > a:focus,
                        .navbar-inverse .navbar-nav > li > a:hover {
                        color: #fff;
                        background-color: transparent;
                        }
                        .navbar-inverse .navbar-nav > .active > a,
                        .navbar-inverse .navbar-nav > .active > a:focus,
                        .navbar-inverse .navbar-nav > .active > a:hover {
                        color: #fff;
                        background-color: #080808;
                        }
                        .navbar-inverse .navbar-nav > .disabled > a,
                        .navbar-inverse .navbar-nav > .disabled > a:focus,
                        .navbar-inverse .navbar-nav > .disabled > a:hover {
                        color: #444;
                        background-color: transparent;
                        }
                        .navbar-inverse .navbar-toggle {
                        border-color: #333;
                        }
                        .navbar-inverse .navbar-toggle:focus,
                        .navbar-inverse .navbar-toggle:hover {
                        background-color: #333;
                        }
                        .navbar-inverse .navbar-toggle .icon-bar {
                        background-color: #fff;
                        }
                        .navbar-inverse .navbar-collapse,
                        .navbar-inverse .navbar-form {
                        border-color: #101010;
                        }
                        .navbar-inverse .navbar-nav > .open > a,
                        .navbar-inverse .navbar-nav > .open > a:focus,
                        .navbar-inverse .navbar-nav > .open > a:hover {
                        color: #fff;
                        background-color: #080808;
                        }
                        @media (max-width: 767px) {
                        .navbar-inverse .navbar-nav .open .dropdown-menu > .dropdown-header {
                            border-color: #080808;
                        }
                        .navbar-inverse .navbar-nav .open .dropdown-menu .divider {
                            background-color: #080808;
                        }
                        .navbar-inverse .navbar-nav .open .dropdown-menu > li > a {
                            color: #9d9d9d;
                        }
                        .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:focus,
                        .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:hover {
                            color: #fff;
                            background-color: transparent;
                        }
                        .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a,
                        .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:focus,
                        .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:hover {
                            color: #fff;
                            background-color: #080808;
                        }
                        .navbar-inverse .navbar-nav .open .dropdown-menu > .disabled > a,
                        .navbar-inverse .navbar-nav .open .dropdown-menu > .disabled > a:focus,
                        .navbar-inverse .navbar-nav .open .dropdown-menu > .disabled > a:hover {
                            color: #444;
                            background-color: transparent;
                        }
                        }
                        .navbar-inverse .navbar-link {
                        color: #9d9d9d;
                        }
                        .navbar-inverse .navbar-link:hover {
                        color: #fff;
                        }
                        .navbar-inverse .btn-link {
                        color: #9d9d9d;
                        }
                        .navbar-inverse .btn-link:focus,
                        .navbar-inverse .btn-link:hover {
                        color: #fff;
                        }
                        .navbar-inverse .btn-link[disabled]:focus,
                        .navbar-inverse .btn-link[disabled]:hover,
                        fieldset[disabled] .navbar-inverse .btn-link:focus,
                        fieldset[disabled] .navbar-inverse .btn-link:hover {
                        color: #444;
                        }
                        .breadcrumb {
                        padding: 8px 15px;
                        margin-bottom: 20px;
                        list-style: none;
                        background-color: #f5f5f5;
                        border-radius: 4px;
                        }
                        .breadcrumb > li {
                        display: inline-block;
                        }
                        .breadcrumb > li + li:before {
                        padding: 0 5px;
                        color: #ccc;
                        content: "/\00a0";
                        }
                        .breadcrumb > .active {
                        color: #777;
                        }
                        .pagination {
                        display: inline-block;
                        padding-left: 0;
                        margin: 20px 0;
                        border-radius: 4px;
                        }
                        .pagination > li {
                        display: inline;
                        }
                        .pagination > li > a,
                        .pagination > li > span {
                        position: relative;
                        float: left;
                        padding: 6px 12px;
                        margin-left: -1px;
                        line-height: 1.42857143;
                        color: #337ab7;
                        text-decoration: none;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        }
                        .pagination > li:first-child > a,
                        .pagination > li:first-child > span {
                        margin-left: 0;
                        border-top-left-radius: 4px;
                        border-bottom-left-radius: 4px;
                        }
                        .pagination > li:last-child > a,
                        .pagination > li:last-child > span {
                        border-top-right-radius: 4px;
                        border-bottom-right-radius: 4px;
                        }
                        .pagination > li > a:focus,
                        .pagination > li > a:hover,
                        .pagination > li > span:focus,
                        .pagination > li > span:hover {
                        z-index: 2;
                        color: #23527c;
                        background-color: #eee;
                        border-color: #ddd;
                        }
                        .pagination > .active > a,
                        .pagination > .active > a:focus,
                        .pagination > .active > a:hover,
                        .pagination > .active > span,
                        .pagination > .active > span:focus,
                        .pagination > .active > span:hover {
                        z-index: 3;
                        color: #fff;
                        cursor: default;
                        background-color: #337ab7;
                        border-color: #337ab7;
                        }
                        .pagination > .disabled > a,
                        .pagination > .disabled > a:focus,
                        .pagination > .disabled > a:hover,
                        .pagination > .disabled > span,
                        .pagination > .disabled > span:focus,
                        .pagination > .disabled > span:hover {
                        color: #777;
                        cursor: not-allowed;
                        background-color: #fff;
                        border-color: #ddd;
                        }
                        .pagination-lg > li > a,
                        .pagination-lg > li > span {
                        padding: 10px 16px;
                        font-size: 18px;
                        line-height: 1.3333333;
                        }
                        .pagination-lg > li:first-child > a,
                        .pagination-lg > li:first-child > span {
                        border-top-left-radius: 6px;
                        border-bottom-left-radius: 6px;
                        }
                        .pagination-lg > li:last-child > a,
                        .pagination-lg > li:last-child > span {
                        border-top-right-radius: 6px;
                        border-bottom-right-radius: 6px;
                        }
                        .pagination-sm > li > a,
                        .pagination-sm > li > span {
                        padding: 5px 10px;
                        font-size: 12px;
                        line-height: 1.5;
                        }
                        .pagination-sm > li:first-child > a,
                        .pagination-sm > li:first-child > span {
                        border-top-left-radius: 3px;
                        border-bottom-left-radius: 3px;
                        }
                        .pagination-sm > li:last-child > a,
                        .pagination-sm > li:last-child > span {
                        border-top-right-radius: 3px;
                        border-bottom-right-radius: 3px;
                        }
                        .pager {
                        padding-left: 0;
                        margin: 20px 0;
                        text-align: center;
                        list-style: none;
                        }
                        .pager li {
                        display: inline;
                        }
                        .pager li > a,
                        .pager li > span {
                        display: inline-block;
                        padding: 5px 14px;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        border-radius: 15px;
                        }
                        .pager li > a:focus,
                        .pager li > a:hover {
                        text-decoration: none;
                        background-color: #eee;
                        }
                        .pager .next > a,
                        .pager .next > span {
                        float: right;
                        }
                        .pager .previous > a,
                        .pager .previous > span {
                        float: left;
                        }
                        .pager .disabled > a,
                        .pager .disabled > a:focus,
                        .pager .disabled > a:hover,
                        .pager .disabled > span {
                        color: #777;
                        cursor: not-allowed;
                        background-color: #fff;
                        }
                        .label {
                        display: inline;
                        padding: 0.2em 0.6em 0.3em;
                        font-size: 75%;
                        font-weight: 700;
                        line-height: 1;
                        color: #fff;
                        text-align: center;
                        white-space: nowrap;
                        vertical-align: baseline;
                        border-radius: 0.25em;
                        }
                        a.label:focus,
                        a.label:hover {
                        color: #fff;
                        text-decoration: none;
                        cursor: pointer;
                        }
                        .label:empty {
                        display: none;
                        }
                        .btn .label {
                        position: relative;
                        top: -1px;
                        }
                        .label-default {
                        background-color: #777;
                        }
                        .label-default[href]:focus,
                        .label-default[href]:hover {
                        background-color: #5e5e5e;
                        }
                        .label-primary {
                        background-color: #337ab7;
                        }
                        .label-primary[href]:focus,
                        .label-primary[href]:hover {
                        background-color: #286090;
                        }
                        .label-success {
                        background-color: #5cb85c;
                        }
                        .label-success[href]:focus,
                        .label-success[href]:hover {
                        background-color: #449d44;
                        }
                        .label-info {
                        background-color: #5bc0de;
                        }
                        .label-info[href]:focus,
                        .label-info[href]:hover {
                        background-color: #31b0d5;
                        }
                        .label-warning {
                        background-color: #f0ad4e;
                        }
                        .label-warning[href]:focus,
                        .label-warning[href]:hover {
                        background-color: #ec971f;
                        }
                        .label-danger {
                        background-color: #d9534f;
                        }
                        .label-danger[href]:focus,
                        .label-danger[href]:hover {
                        background-color: #c9302c;
                        }
                        .badge {
                        display: inline-block;
                        min-width: 10px;
                        padding: 3px 7px;
                        font-size: 12px;
                        font-weight: 700;
                        line-height: 1;
                        color: #fff;
                        text-align: center;
                        white-space: nowrap;
                        vertical-align: middle;
                        background-color: #777;
                        border-radius: 10px;
                        }
                        .badge:empty {
                        display: none;
                        }
                        .btn .badge {
                        position: relative;
                        top: -1px;
                        }
                        .btn-group-xs > .btn .badge,
                        .btn-xs .badge {
                        top: 0;
                        padding: 1px 5px;
                        }
                        a.badge:focus,
                        a.badge:hover {
                        color: #fff;
                        text-decoration: none;
                        cursor: pointer;
                        }
                        .list-group-item.active > .badge,
                        .nav-pills > .active > a > .badge {
                        color: #337ab7;
                        background-color: #fff;
                        }
                        .list-group-item > .badge {
                        float: right;
                        }
                        .list-group-item > .badge + .badge {
                        margin-right: 5px;
                        }
                        .nav-pills > li > a > .badge {
                        margin-left: 3px;
                        }
                        .jumbotron {
                        padding-top: 30px;
                        padding-bottom: 30px;
                        margin-bottom: 30px;
                        color: inherit;
                        background-color: #eee;
                        }
                        .jumbotron .h1,
                        .jumbotron h1 {
                        color: inherit;
                        }
                        .jumbotron p {
                        margin-bottom: 15px;
                        font-size: 21px;
                        font-weight: 200;
                        }
                        .jumbotron > hr {
                        border-top-color: #d5d5d5;
                        }
                        .container .jumbotron,
                        .container-fluid .jumbotron {
                        padding-right: 15px;
                        padding-left: 15px;
                        border-radius: 6px;
                        }
                        .jumbotron .container {
                        max-width: 100%;
                        }
                        @media screen and (min-width: 768px) {
                        .jumbotron {
                            padding-top: 48px;
                            padding-bottom: 48px;
                        }
                        .container .jumbotron,
                        .container-fluid .jumbotron {
                            padding-right: 60px;
                            padding-left: 60px;
                        }
                        .jumbotron .h1,
                        .jumbotron h1 {
                            font-size: 63px;
                        }
                        }
                        .thumbnail {
                        display: block;
                        padding: 4px;
                        margin-bottom: 20px;
                        line-height: 1.42857143;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        -webkit-transition: border 0.2s ease-in-out;
                        -o-transition: border 0.2s ease-in-out;
                        transition: border 0.2s ease-in-out;
                        }
                        .thumbnail a > img,
                        .thumbnail > img {
                        margin-right: auto;
                        margin-left: auto;
                        }
                        a.thumbnail.active,
                        a.thumbnail:focus,
                        a.thumbnail:hover {
                        border-color: #337ab7;
                        }
                        .thumbnail .caption {
                        padding: 9px;
                        color: #333;
                        }
                        .alert {
                        padding: 15px;
                        margin-bottom: 20px;
                        border: 1px solid transparent;
                        border-radius: 4px;
                        }
                        .alert h4 {
                        margin-top: 0;
                        color: inherit;
                        }
                        .alert .alert-link {
                        font-weight: 700;
                        }
                        .alert > p,
                        .alert > ul {
                        margin-bottom: 0;
                        }
                        .alert > p + p {
                        margin-top: 5px;
                        }
                        .alert-dismissable,
                        .alert-dismissible {
                        padding-right: 35px;
                        }
                        .alert-dismissable .close,
                        .alert-dismissible .close {
                        position: relative;
                        top: -2px;
                        right: -21px;
                        color: inherit;
                        }
                        .alert-success {
                        color: #3c763d;
                        background-color: #dff0d8;
                        border-color: #d6e9c6;
                        }
                        .alert-success hr {
                        border-top-color: #c9e2b3;
                        }
                        .alert-success .alert-link {
                        color: #2b542c;
                        }
                        .alert-info {
                        color: #31708f;
                        background-color: #d9edf7;
                        border-color: #bce8f1;
                        }
                        .alert-info hr {
                        border-top-color: #a6e1ec;
                        }
                        .alert-info .alert-link {
                        color: #245269;
                        }
                        .alert-warning {
                        color: #8a6d3b;
                        background-color: #fcf8e3;
                        border-color: #faebcc;
                        }
                        .alert-warning hr {
                        border-top-color: #f7e1b5;
                        }
                        .alert-warning .alert-link {
                        color: #66512c;
                        }
                        .alert-danger {
                        color: #a94442;
                        background-color: #f2dede;
                        border-color: #ebccd1;
                        }
                        .alert-danger hr {
                        border-top-color: #e4b9c0;
                        }
                        .alert-danger .alert-link {
                        color: #843534;
                        }
                        @-webkit-keyframes progress-bar-stripes {
                        from {
                            background-position: 40px 0;
                        }
                        to {
                            background-position: 0 0;
                        }
                        }
                        @-o-keyframes progress-bar-stripes {
                        from {
                            background-position: 40px 0;
                        }
                        to {
                            background-position: 0 0;
                        }
                        }
                        @keyframes progress-bar-stripes {
                        from {
                            background-position: 40px 0;
                        }
                        to {
                            background-position: 0 0;
                        }
                        }
                        .progress {
                        height: 20px;
                        margin-bottom: 20px;
                        overflow: hidden;
                        background-color: #f5f5f5;
                        border-radius: 4px;
                        -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
                        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
                        }
                        .progress-bar {
                        float: left;
                        width: 0;
                        height: 100%;
                        font-size: 12px;
                        line-height: 20px;
                        color: #fff;
                        text-align: center;
                        background-color: #337ab7;
                        -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
                        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
                        -webkit-transition: width 0.6s ease;
                        -o-transition: width 0.6s ease;
                        transition: width 0.6s ease;
                        }
                        .progress-bar-striped,
                        .progress-striped .progress-bar {
                        background-image: -webkit-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: -o-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        -webkit-background-size: 40px 40px;
                        background-size: 40px 40px;
                        }
                        .progress-bar.active,
                        .progress.active .progress-bar {
                        -webkit-animation: progress-bar-stripes 2s linear infinite;
                        -o-animation: progress-bar-stripes 2s linear infinite;
                        animation: progress-bar-stripes 2s linear infinite;
                        }
                        .progress-bar-success {
                        background-color: #5cb85c;
                        }
                        .progress-striped .progress-bar-success {
                        background-image: -webkit-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: -o-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        }
                        .progress-bar-info {
                        background-color: #5bc0de;
                        }
                        .progress-striped .progress-bar-info {
                        background-image: -webkit-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: -o-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        }
                        .progress-bar-warning {
                        background-color: #f0ad4e;
                        }
                        .progress-striped .progress-bar-warning {
                        background-image: -webkit-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: -o-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        }
                        .progress-bar-danger {
                        background-color: #d9534f;
                        }
                        .progress-striped .progress-bar-danger {
                        background-image: -webkit-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: -o-linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        background-image: linear-gradient(
                            45deg,
                            rgba(255, 255, 255, 0.15) 25%,
                            transparent 25%,
                            transparent 50%,
                            rgba(255, 255, 255, 0.15) 50%,
                            rgba(255, 255, 255, 0.15) 75%,
                            transparent 75%,
                            transparent
                        );
                        }
                        .media {
                        margin-top: 15px;
                        }
                        .media:first-child {
                        margin-top: 0;
                        }
                        .media,
                        .media-body {
                        overflow: hidden;
                        zoom: 1;
                        }
                        .media-body {
                        width: 10000px;
                        }
                        .media-object {
                        display: block;
                        }
                        .media-object.img-thumbnail {
                        max-width: none;
                        }
                        .media-right,
                        .media > .pull-right {
                        padding-left: 10px;
                        }
                        .media-left,
                        .media > .pull-left {
                        padding-right: 10px;
                        }
                        .media-body,
                        .media-left,
                        .media-right {
                        display: table-cell;
                        vertical-align: top;
                        }
                        .media-middle {
                        vertical-align: middle;
                        }
                        .media-bottom {
                        vertical-align: bottom;
                        }
                        .media-heading {
                        margin-top: 0;
                        margin-bottom: 5px;
                        }
                        .media-list {
                        padding-left: 0;
                        list-style: none;
                        }
                        .list-group {
                        padding-left: 0;
                        margin-bottom: 20px;
                        }
                        .list-group-item {
                        position: relative;
                        display: block;
                        padding: 10px 15px;
                        margin-bottom: -1px;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        }
                        .list-group-item:first-child {
                        border-top-left-radius: 4px;
                        border-top-right-radius: 4px;
                        }
                        .list-group-item:last-child {
                        margin-bottom: 0;
                        border-bottom-right-radius: 4px;
                        border-bottom-left-radius: 4px;
                        }
                        a.list-group-item,
                        button.list-group-item {
                        color: #555;
                        }
                        a.list-group-item .list-group-item-heading,
                        button.list-group-item .list-group-item-heading {
                        color: #333;
                        }
                        a.list-group-item:focus,
                        a.list-group-item:hover,
                        button.list-group-item:focus,
                        button.list-group-item:hover {
                        color: #555;
                        text-decoration: none;
                        background-color: #f5f5f5;
                        }
                        button.list-group-item {
                        width: 100%;
                        text-align: left;
                        }
                        .list-group-item.disabled,
                        .list-group-item.disabled:focus,
                        .list-group-item.disabled:hover {
                        color: #777;
                        cursor: not-allowed;
                        background-color: #eee;
                        }
                        .list-group-item.disabled .list-group-item-heading,
                        .list-group-item.disabled:focus .list-group-item-heading,
                        .list-group-item.disabled:hover .list-group-item-heading {
                        color: inherit;
                        }
                        .list-group-item.disabled .list-group-item-text,
                        .list-group-item.disabled:focus .list-group-item-text,
                        .list-group-item.disabled:hover .list-group-item-text {
                        color: #777;
                        }
                        .list-group-item.active,
                        .list-group-item.active:focus,
                        .list-group-item.active:hover {
                        z-index: 2;
                        color: #fff;
                        background-color: #337ab7;
                        border-color: #337ab7;
                        }
                        .list-group-item.active .list-group-item-heading,
                        .list-group-item.active .list-group-item-heading > .small,
                        .list-group-item.active .list-group-item-heading > small,
                        .list-group-item.active:focus .list-group-item-heading,
                        .list-group-item.active:focus .list-group-item-heading > .small,
                        .list-group-item.active:focus .list-group-item-heading > small,
                        .list-group-item.active:hover .list-group-item-heading,
                        .list-group-item.active:hover .list-group-item-heading > .small,
                        .list-group-item.active:hover .list-group-item-heading > small {
                        color: inherit;
                        }
                        .list-group-item.active .list-group-item-text,
                        .list-group-item.active:focus .list-group-item-text,
                        .list-group-item.active:hover .list-group-item-text {
                        color: #c7ddef;
                        }
                        .list-group-item-success {
                        color: #3c763d;
                        background-color: #dff0d8;
                        }
                        a.list-group-item-success,
                        button.list-group-item-success {
                        color: #3c763d;
                        }
                        a.list-group-item-success .list-group-item-heading,
                        button.list-group-item-success .list-group-item-heading {
                        color: inherit;
                        }
                        a.list-group-item-success:focus,
                        a.list-group-item-success:hover,
                        button.list-group-item-success:focus,
                        button.list-group-item-success:hover {
                        color: #3c763d;
                        background-color: #d0e9c6;
                        }
                        a.list-group-item-success.active,
                        a.list-group-item-success.active:focus,
                        a.list-group-item-success.active:hover,
                        button.list-group-item-success.active,
                        button.list-group-item-success.active:focus,
                        button.list-group-item-success.active:hover {
                        color: #fff;
                        background-color: #3c763d;
                        border-color: #3c763d;
                        }
                        .list-group-item-info {
                        color: #31708f;
                        background-color: #d9edf7;
                        }
                        a.list-group-item-info,
                        button.list-group-item-info {
                        color: #31708f;
                        }
                        a.list-group-item-info .list-group-item-heading,
                        button.list-group-item-info .list-group-item-heading {
                        color: inherit;
                        }
                        a.list-group-item-info:focus,
                        a.list-group-item-info:hover,
                        button.list-group-item-info:focus,
                        button.list-group-item-info:hover {
                        color: #31708f;
                        background-color: #c4e3f3;
                        }
                        a.list-group-item-info.active,
                        a.list-group-item-info.active:focus,
                        a.list-group-item-info.active:hover,
                        button.list-group-item-info.active,
                        button.list-group-item-info.active:focus,
                        button.list-group-item-info.active:hover {
                        color: #fff;
                        background-color: #31708f;
                        border-color: #31708f;
                        }
                        .list-group-item-warning {
                        color: #8a6d3b;
                        background-color: #fcf8e3;
                        }
                        a.list-group-item-warning,
                        button.list-group-item-warning {
                        color: #8a6d3b;
                        }
                        a.list-group-item-warning .list-group-item-heading,
                        button.list-group-item-warning .list-group-item-heading {
                        color: inherit;
                        }
                        a.list-group-item-warning:focus,
                        a.list-group-item-warning:hover,
                        button.list-group-item-warning:focus,
                        button.list-group-item-warning:hover {
                        color: #8a6d3b;
                        background-color: #faf2cc;
                        }
                        a.list-group-item-warning.active,
                        a.list-group-item-warning.active:focus,
                        a.list-group-item-warning.active:hover,
                        button.list-group-item-warning.active,
                        button.list-group-item-warning.active:focus,
                        button.list-group-item-warning.active:hover {
                        color: #fff;
                        background-color: #8a6d3b;
                        border-color: #8a6d3b;
                        }
                        .list-group-item-danger {
                        color: #a94442;
                        background-color: #f2dede;
                        }
                        a.list-group-item-danger,
                        button.list-group-item-danger {
                        color: #a94442;
                        }
                        a.list-group-item-danger .list-group-item-heading,
                        button.list-group-item-danger .list-group-item-heading {
                        color: inherit;
                        }
                        a.list-group-item-danger:focus,
                        a.list-group-item-danger:hover,
                        button.list-group-item-danger:focus,
                        button.list-group-item-danger:hover {
                        color: #a94442;
                        background-color: #ebcccc;
                        }
                        a.list-group-item-danger.active,
                        a.list-group-item-danger.active:focus,
                        a.list-group-item-danger.active:hover,
                        button.list-group-item-danger.active,
                        button.list-group-item-danger.active:focus,
                        button.list-group-item-danger.active:hover {
                        color: #fff;
                        background-color: #a94442;
                        border-color: #a94442;
                        }
                        .list-group-item-heading {
                        margin-top: 0;
                        margin-bottom: 5px;
                        }
                        .list-group-item-text {
                        margin-bottom: 0;
                        line-height: 1.3;
                        }
                        .panel {
                        margin-bottom: 20px;
                        background-color: #fff;
                        border: 1px solid transparent;
                        border-radius: 4px;
                        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
                        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
                        }
                        .panel-body {
                        padding: 15px;
                        }
                        .panel-heading {
                        padding: 10px 15px;
                        border-bottom: 1px solid transparent;
                        border-top-left-radius: 3px;
                        border-top-right-radius: 3px;
                        }
                        .panel-heading > .dropdown .dropdown-toggle {
                        color: inherit;
                        }
                        .panel-title {
                        margin-top: 0;
                        margin-bottom: 0;
                        font-size: 16px;
                        color: inherit;
                        }
                        .panel-title > .small,
                        .panel-title > .small > a,
                        .panel-title > a,
                        .panel-title > small,
                        .panel-title > small > a {
                        color: inherit;
                        }
                        .panel-footer {
                        padding: 10px 15px;
                        background-color: #f5f5f5;
                        border-top: 1px solid #ddd;
                        border-bottom-right-radius: 3px;
                        border-bottom-left-radius: 3px;
                        }
                        .panel > .list-group,
                        .panel > .panel-collapse > .list-group {
                        margin-bottom: 0;
                        }
                        .panel > .list-group .list-group-item,
                        .panel > .panel-collapse > .list-group .list-group-item {
                        border-width: 1px 0;
                        border-radius: 0;
                        }
                        .panel > .list-group:first-child .list-group-item:first-child,
                        .panel
                        > .panel-collapse
                        > .list-group:first-child
                        .list-group-item:first-child {
                        border-top: 0;
                        border-top-left-radius: 3px;
                        border-top-right-radius: 3px;
                        }
                        .panel > .list-group:last-child .list-group-item:last-child,
                        .panel > .panel-collapse > .list-group:last-child .list-group-item:last-child {
                        border-bottom: 0;
                        border-bottom-right-radius: 3px;
                        border-bottom-left-radius: 3px;
                        }
                        .panel
                        > .panel-heading
                        + .panel-collapse
                        > .list-group
                        .list-group-item:first-child {
                        border-top-left-radius: 0;
                        border-top-right-radius: 0;
                        }
                        .panel-heading + .list-group .list-group-item:first-child {
                        border-top-width: 0;
                        }
                        .list-group + .panel-footer {
                        border-top-width: 0;
                        }
                        .panel > .panel-collapse > .table,
                        .panel > .table,
                        .panel > .table-responsive > .table {
                        margin-bottom: 0;
                        }
                        .panel > .panel-collapse > .table caption,
                        .panel > .table caption,
                        .panel > .table-responsive > .table caption {
                        padding-right: 15px;
                        padding-left: 15px;
                        }
                        .panel > .table-responsive:first-child > .table:first-child,
                        .panel > .table:first-child {
                        border-top-left-radius: 3px;
                        border-top-right-radius: 3px;
                        }
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > tbody:first-child
                        > tr:first-child,
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > thead:first-child
                        > tr:first-child,
                        .panel > .table:first-child > tbody:first-child > tr:first-child,
                        .panel > .table:first-child > thead:first-child > tr:first-child {
                        border-top-left-radius: 3px;
                        border-top-right-radius: 3px;
                        }
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > tbody:first-child
                        > tr:first-child
                        td:first-child,
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > tbody:first-child
                        > tr:first-child
                        th:first-child,
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > thead:first-child
                        > tr:first-child
                        td:first-child,
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > thead:first-child
                        > tr:first-child
                        th:first-child,
                        .panel > .table:first-child > tbody:first-child > tr:first-child td:first-child,
                        .panel > .table:first-child > tbody:first-child > tr:first-child th:first-child,
                        .panel > .table:first-child > thead:first-child > tr:first-child td:first-child,
                        .panel
                        > .table:first-child
                        > thead:first-child
                        > tr:first-child
                        th:first-child {
                        border-top-left-radius: 3px;
                        }
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > tbody:first-child
                        > tr:first-child
                        td:last-child,
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > tbody:first-child
                        > tr:first-child
                        th:last-child,
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > thead:first-child
                        > tr:first-child
                        td:last-child,
                        .panel
                        > .table-responsive:first-child
                        > .table:first-child
                        > thead:first-child
                        > tr:first-child
                        th:last-child,
                        .panel > .table:first-child > tbody:first-child > tr:first-child td:last-child,
                        .panel > .table:first-child > tbody:first-child > tr:first-child th:last-child,
                        .panel > .table:first-child > thead:first-child > tr:first-child td:last-child,
                        .panel > .table:first-child > thead:first-child > tr:first-child th:last-child {
                        border-top-right-radius: 3px;
                        }
                        .panel > .table-responsive:last-child > .table:last-child,
                        .panel > .table:last-child {
                        border-bottom-right-radius: 3px;
                        border-bottom-left-radius: 3px;
                        }
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tbody:last-child
                        > tr:last-child,
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tfoot:last-child
                        > tr:last-child,
                        .panel > .table:last-child > tbody:last-child > tr:last-child,
                        .panel > .table:last-child > tfoot:last-child > tr:last-child {
                        border-bottom-right-radius: 3px;
                        border-bottom-left-radius: 3px;
                        }
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tbody:last-child
                        > tr:last-child
                        td:first-child,
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tbody:last-child
                        > tr:last-child
                        th:first-child,
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tfoot:last-child
                        > tr:last-child
                        td:first-child,
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tfoot:last-child
                        > tr:last-child
                        th:first-child,
                        .panel > .table:last-child > tbody:last-child > tr:last-child td:first-child,
                        .panel > .table:last-child > tbody:last-child > tr:last-child th:first-child,
                        .panel > .table:last-child > tfoot:last-child > tr:last-child td:first-child,
                        .panel > .table:last-child > tfoot:last-child > tr:last-child th:first-child {
                        border-bottom-left-radius: 3px;
                        }
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tbody:last-child
                        > tr:last-child
                        td:last-child,
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tbody:last-child
                        > tr:last-child
                        th:last-child,
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tfoot:last-child
                        > tr:last-child
                        td:last-child,
                        .panel
                        > .table-responsive:last-child
                        > .table:last-child
                        > tfoot:last-child
                        > tr:last-child
                        th:last-child,
                        .panel > .table:last-child > tbody:last-child > tr:last-child td:last-child,
                        .panel > .table:last-child > tbody:last-child > tr:last-child th:last-child,
                        .panel > .table:last-child > tfoot:last-child > tr:last-child td:last-child,
                        .panel > .table:last-child > tfoot:last-child > tr:last-child th:last-child {
                        border-bottom-right-radius: 3px;
                        }
                        .panel > .panel-body + .table,
                        .panel > .panel-body + .table-responsive,
                        .panel > .table + .panel-body,
                        .panel > .table-responsive + .panel-body {
                        border-top: 1px solid #ddd;
                        }
                        .panel > .table > tbody:first-child > tr:first-child td,
                        .panel > .table > tbody:first-child > tr:first-child th {
                        border-top: 0;
                        }
                        .panel > .table-bordered,
                        .panel > .table-responsive > .table-bordered {
                        border: 0;
                        }
                        .panel > .table-bordered > tbody > tr > td:first-child,
                        .panel > .table-bordered > tbody > tr > th:first-child,
                        .panel > .table-bordered > tfoot > tr > td:first-child,
                        .panel > .table-bordered > tfoot > tr > th:first-child,
                        .panel > .table-bordered > thead > tr > td:first-child,
                        .panel > .table-bordered > thead > tr > th:first-child,
                        .panel > .table-responsive > .table-bordered > tbody > tr > td:first-child,
                        .panel > .table-responsive > .table-bordered > tbody > tr > th:first-child,
                        .panel > .table-responsive > .table-bordered > tfoot > tr > td:first-child,
                        .panel > .table-responsive > .table-bordered > tfoot > tr > th:first-child,
                        .panel > .table-responsive > .table-bordered > thead > tr > td:first-child,
                        .panel > .table-responsive > .table-bordered > thead > tr > th:first-child {
                        border-left: 0;
                        }
                        .panel > .table-bordered > tbody > tr > td:last-child,
                        .panel > .table-bordered > tbody > tr > th:last-child,
                        .panel > .table-bordered > tfoot > tr > td:last-child,
                        .panel > .table-bordered > tfoot > tr > th:last-child,
                        .panel > .table-bordered > thead > tr > td:last-child,
                        .panel > .table-bordered > thead > tr > th:last-child,
                        .panel > .table-responsive > .table-bordered > tbody > tr > td:last-child,
                        .panel > .table-responsive > .table-bordered > tbody > tr > th:last-child,
                        .panel > .table-responsive > .table-bordered > tfoot > tr > td:last-child,
                        .panel > .table-responsive > .table-bordered > tfoot > tr > th:last-child,
                        .panel > .table-responsive > .table-bordered > thead > tr > td:last-child,
                        .panel > .table-responsive > .table-bordered > thead > tr > th:last-child {
                        border-right: 0;
                        }
                        .panel > .table-bordered > tbody > tr:first-child > td,
                        .panel > .table-bordered > tbody > tr:first-child > th,
                        .panel > .table-bordered > thead > tr:first-child > td,
                        .panel > .table-bordered > thead > tr:first-child > th,
                        .panel > .table-responsive > .table-bordered > tbody > tr:first-child > td,
                        .panel > .table-responsive > .table-bordered > tbody > tr:first-child > th,
                        .panel > .table-responsive > .table-bordered > thead > tr:first-child > td,
                        .panel > .table-responsive > .table-bordered > thead > tr:first-child > th {
                        border-bottom: 0;
                        }
                        .panel > .table-bordered > tbody > tr:last-child > td,
                        .panel > .table-bordered > tbody > tr:last-child > th,
                        .panel > .table-bordered > tfoot > tr:last-child > td,
                        .panel > .table-bordered > tfoot > tr:last-child > th,
                        .panel > .table-responsive > .table-bordered > tbody > tr:last-child > td,
                        .panel > .table-responsive > .table-bordered > tbody > tr:last-child > th,
                        .panel > .table-responsive > .table-bordered > tfoot > tr:last-child > td,
                        .panel > .table-responsive > .table-bordered > tfoot > tr:last-child > th {
                        border-bottom: 0;
                        }
                        .panel > .table-responsive {
                        margin-bottom: 0;
                        border: 0;
                        }
                        .panel-group {
                        margin-bottom: 20px;
                        }
                        .panel-group .panel {
                        margin-bottom: 0;
                        border-radius: 4px;
                        }
                        .panel-group .panel + .panel {
                        margin-top: 5px;
                        }
                        .panel-group .panel-heading {
                        border-bottom: 0;
                        }
                        .panel-group .panel-heading + .panel-collapse > .list-group,
                        .panel-group .panel-heading + .panel-collapse > .panel-body {
                        border-top: 1px solid #ddd;
                        }
                        .panel-group .panel-footer {
                        border-top: 0;
                        }
                        .panel-group .panel-footer + .panel-collapse .panel-body {
                        border-bottom: 1px solid #ddd;
                        }
                        .panel-default {
                        border-color: #ddd;
                        }
                        .panel-default > .panel-heading {
                        color: #333;
                        background-color: #f5f5f5;
                        border-color: #ddd;
                        }
                        .panel-default > .panel-heading + .panel-collapse > .panel-body {
                        border-top-color: #ddd;
                        }
                        .panel-default > .panel-heading .badge {
                        color: #f5f5f5;
                        background-color: #333;
                        }
                        .panel-default > .panel-footer + .panel-collapse > .panel-body {
                        border-bottom-color: #ddd;
                        }
                        .panel-primary {
                        border-color: #337ab7;
                        }
                        .panel-primary > .panel-heading {
                        color: #fff;
                        background-color: #337ab7;
                        border-color: #337ab7;
                        }
                        .panel-primary > .panel-heading + .panel-collapse > .panel-body {
                        border-top-color: #337ab7;
                        }
                        .panel-primary > .panel-heading .badge {
                        color: #337ab7;
                        background-color: #fff;
                        }
                        .panel-primary > .panel-footer + .panel-collapse > .panel-body {
                        border-bottom-color: #337ab7;
                        }
                        .panel-success {
                        border-color: #d6e9c6;
                        }
                        .panel-success > .panel-heading {
                        color: #3c763d;
                        background-color: #dff0d8;
                        border-color: #d6e9c6;
                        }
                        .panel-success > .panel-heading + .panel-collapse > .panel-body {
                        border-top-color: #d6e9c6;
                        }
                        .panel-success > .panel-heading .badge {
                        color: #dff0d8;
                        background-color: #3c763d;
                        }
                        .panel-success > .panel-footer + .panel-collapse > .panel-body {
                        border-bottom-color: #d6e9c6;
                        }
                        .panel-info {
                        border-color: #bce8f1;
                        }
                        .panel-info > .panel-heading {
                        color: #31708f;
                        background-color: #d9edf7;
                        border-color: #bce8f1;
                        }
                        .panel-info > .panel-heading + .panel-collapse > .panel-body {
                        border-top-color: #bce8f1;
                        }
                        .panel-info > .panel-heading .badge {
                        color: #d9edf7;
                        background-color: #31708f;
                        }
                        .panel-info > .panel-footer + .panel-collapse > .panel-body {
                        border-bottom-color: #bce8f1;
                        }
                        .panel-warning {
                        border-color: #faebcc;
                        }
                        .panel-warning > .panel-heading {
                        color: #8a6d3b;
                        background-color: #fcf8e3;
                        border-color: #faebcc;
                        }
                        .panel-warning > .panel-heading + .panel-collapse > .panel-body {
                        border-top-color: #faebcc;
                        }
                        .panel-warning > .panel-heading .badge {
                        color: #fcf8e3;
                        background-color: #8a6d3b;
                        }
                        .panel-warning > .panel-footer + .panel-collapse > .panel-body {
                        border-bottom-color: #faebcc;
                        }
                        .panel-danger {
                        border-color: #ebccd1;
                        }
                        .panel-danger > .panel-heading {
                        color: #a94442;
                        background-color: #f2dede;
                        border-color: #ebccd1;
                        }
                        .panel-danger > .panel-heading + .panel-collapse > .panel-body {
                        border-top-color: #ebccd1;
                        }
                        .panel-danger > .panel-heading .badge {
                        color: #f2dede;
                        background-color: #a94442;
                        }
                        .panel-danger > .panel-footer + .panel-collapse > .panel-body {
                        border-bottom-color: #ebccd1;
                        }
                        .embed-responsive {
                        position: relative;
                        display: block;
                        height: 0;
                        padding: 0;
                        overflow: hidden;
                        }
                        .embed-responsive .embed-responsive-item,
                        .embed-responsive embed,
                        .embed-responsive iframe,
                        .embed-responsive object,
                        .embed-responsive video {
                        position: absolute;
                        top: 0;
                        bottom: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        border: 0;
                        }
                        .embed-responsive-16by9 {
                        padding-bottom: 56.25%;
                        }
                        .embed-responsive-4by3 {
                        padding-bottom: 75%;
                        }
                        .well {
                        min-height: 20px;
                        padding: 19px;
                        margin-bottom: 20px;
                        background-color: #f5f5f5;
                        border: 1px solid #e3e3e3;
                        border-radius: 4px;
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
                        }
                        .well blockquote {
                        border-color: #ddd;
                        border-color: rgba(0, 0, 0, 0.15);
                        }
                        .well-lg {
                        padding: 24px;
                        border-radius: 6px;
                        }
                        .well-sm {
                        padding: 9px;
                        border-radius: 3px;
                        }
                        .close {
                        float: right;
                        font-size: 21px;
                        font-weight: 700;
                        line-height: 1;
                        color: #000;
                        text-shadow: 0 1px 0 #fff;
                        opacity: 0.2;
                        }
                        .close:focus,
                        .close:hover {
                        color: #000;
                        text-decoration: none;
                        cursor: pointer;
                        opacity: 0.5;
                        }
                        button.close {
                        -webkit-appearance: none;
                        padding: 0;
                        cursor: pointer;
                        background: 0 0;
                        border: 0;
                        }
                        .modal-open {
                        overflow: hidden;
                        }
                        .modal {
                        position: fixed;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        left: 0;
                        z-index: 1050;
                        display: none;
                        overflow: hidden;
                        -webkit-overflow-scrolling: touch;
                        outline: 0;
                        }
                        .modal.fade .modal-dialog {
                        -webkit-transition: -webkit-transform 0.3s ease-out;
                        -o-transition: -o-transform 0.3s ease-out;
                        transition: transform 0.3s ease-out;
                        -webkit-transform: translate(0, -25%);
                        -ms-transform: translate(0, -25%);
                        -o-transform: translate(0, -25%);
                        transform: translate(0, -25%);
                        }
                        .modal.in .modal-dialog {
                        -webkit-transform: translate(0, 0);
                        -ms-transform: translate(0, 0);
                        -o-transform: translate(0, 0);
                        transform: translate(0, 0);
                        }
                        .modal-open .modal {
                        overflow-x: hidden;
                        overflow-y: auto;
                        }
                        .modal-dialog {
                        position: relative;
                        width: auto;
                        margin: 10px;
                        }
                        .modal-content {
                        position: relative;
                        background-color: #fff;
                        -webkit-background-clip: padding-box;
                        background-clip: padding-box;
                        border: 1px solid #999;
                        border: 1px solid rgba(0, 0, 0, 0.2);
                        border-radius: 6px;
                        outline: 0;
                        -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
                        box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
                        }
                        .modal-backdrop {
                        position: fixed;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        left: 0;
                        z-index: 1040;
                        background-color: #000;
                        }
                        .modal-backdrop.fade {
                        opacity: 0;
                        }
                        .modal-backdrop.in {
                        opacity: 0.5;
                        }
                        .modal-header {
                        padding: 15px;
                        border-bottom: 1px solid #e5e5e5;
                        }
                        .modal-header .close {
                        margin-top: -2px;
                        }
                        .modal-title {
                        margin: 0;
                        line-height: 1.42857143;
                        }
                        .modal-body {
                        position: relative;
                        padding: 15px;
                        }
                        .modal-footer {
                        padding: 15px;
                        text-align: right;
                        border-top: 1px solid #e5e5e5;
                        }
                        .modal-footer .btn + .btn {
                        margin-bottom: 0;
                        margin-left: 5px;
                        }
                        .modal-footer .btn-group .btn + .btn {
                        margin-left: -1px;
                        }
                        .modal-footer .btn-block + .btn-block {
                        margin-left: 0;
                        }
                        .modal-scrollbar-measure {
                        position: absolute;
                        top: -9999px;
                        width: 50px;
                        height: 50px;
                        overflow: scroll;
                        }
                        @media (min-width: 768px) {
                        .modal-dialog {
                            width: 600px;
                            margin: 30px auto;
                        }
                        .modal-content {
                            -webkit-box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
                            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
                        }
                        .modal-sm {
                            width: 300px;
                        }
                        }
                        @media (min-width: 992px) {
                        .modal-lg {
                            width: 900px;
                        }
                        }
                        .tooltip {
                        position: absolute;
                        z-index: 1070;
                        display: block;
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        font-size: 12px;
                        font-style: normal;
                        font-weight: 400;
                        line-height: 1.42857143;
                        text-align: left;
                        text-align: start;
                        text-decoration: none;
                        text-shadow: none;
                        text-transform: none;
                        letter-spacing: normal;
                        word-break: normal;
                        word-spacing: normal;
                        word-wrap: normal;
                        white-space: normal;
                        opacity: 0;
                        line-break: auto;
                        }
                        .tooltip.in {
                        opacity: 0.9;
                        }
                        .tooltip.top {
                        padding: 5px 0;
                        margin-top: -3px;
                        }
                        .tooltip.right {
                        padding: 0 5px;
                        margin-left: 3px;
                        }
                        .tooltip.bottom {
                        padding: 5px 0;
                        margin-top: 3px;
                        }
                        .tooltip.left {
                        padding: 0 5px;
                        margin-left: -3px;
                        }
                        .tooltip-inner {
                        max-width: 200px;
                        padding: 3px 8px;
                        color: #fff;
                        text-align: center;
                        background-color: #000;
                        border-radius: 4px;
                        }
                        .tooltip-arrow {
                        position: absolute;
                        width: 0;
                        height: 0;
                        border-color: transparent;
                        border-style: solid;
                        }
                        .tooltip.top .tooltip-arrow {
                        bottom: 0;
                        left: 50%;
                        margin-left: -5px;
                        border-width: 5px 5px 0;
                        border-top-color: #000;
                        }
                        .tooltip.top-left .tooltip-arrow {
                        right: 5px;
                        bottom: 0;
                        margin-bottom: -5px;
                        border-width: 5px 5px 0;
                        border-top-color: #000;
                        }
                        .tooltip.top-right .tooltip-arrow {
                        bottom: 0;
                        left: 5px;
                        margin-bottom: -5px;
                        border-width: 5px 5px 0;
                        border-top-color: #000;
                        }
                        .tooltip.right .tooltip-arrow {
                        top: 50%;
                        left: 0;
                        margin-top: -5px;
                        border-width: 5px 5px 5px 0;
                        border-right-color: #000;
                        }
                        .tooltip.left .tooltip-arrow {
                        top: 50%;
                        right: 0;
                        margin-top: -5px;
                        border-width: 5px 0 5px 5px;
                        border-left-color: #000;
                        }
                        .tooltip.bottom .tooltip-arrow {
                        top: 0;
                        left: 50%;
                        margin-left: -5px;
                        border-width: 0 5px 5px;
                        border-bottom-color: #000;
                        }
                        .tooltip.bottom-left .tooltip-arrow {
                        top: 0;
                        right: 5px;
                        margin-top: -5px;
                        border-width: 0 5px 5px;
                        border-bottom-color: #000;
                        }
                        .tooltip.bottom-right .tooltip-arrow {
                        top: 0;
                        left: 5px;
                        margin-top: -5px;
                        border-width: 0 5px 5px;
                        border-bottom-color: #000;
                        }
                        .popover {
                        position: absolute;
                        top: 0;
                        left: 0;
                        z-index: 1060;
                        display: none;
                        max-width: 276px;
                        padding: 1px;
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        font-size: 14px;
                        font-style: normal;
                        font-weight: 400;
                        line-height: 1.42857143;
                        text-align: left;
                        text-align: start;
                        text-decoration: none;
                        text-shadow: none;
                        text-transform: none;
                        letter-spacing: normal;
                        word-break: normal;
                        word-spacing: normal;
                        word-wrap: normal;
                        white-space: normal;
                        background-color: #fff;
                        -webkit-background-clip: padding-box;
                        background-clip: padding-box;
                        border: 1px solid #ccc;
                        border: 1px solid rgba(0, 0, 0, 0.2);
                        border-radius: 6px;
                        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                        line-break: auto;
                        }
                        .popover.top {
                        margin-top: -10px;
                        }
                        .popover.right {
                        margin-left: 10px;
                        }
                        .popover.bottom {
                        margin-top: 10px;
                        }
                        .popover.left {
                        margin-left: -10px;
                        }
                        .popover-title {
                        padding: 8px 14px;
                        margin: 0;
                        font-size: 14px;
                        background-color: #f7f7f7;
                        border-bottom: 1px solid #ebebeb;
                        border-radius: 5px 5px 0 0;
                        }
                        .popover-content {
                        padding: 9px 14px;
                        }
                        .popover > .arrow,
                        .popover > .arrow:after {
                        position: absolute;
                        display: block;
                        width: 0;
                        height: 0;
                        border-color: transparent;
                        border-style: solid;
                        }
                        .popover > .arrow {
                        border-width: 11px;
                        }
                        .popover > .arrow:after {
                        content: "";
                        border-width: 10px;
                        }
                        .popover.top > .arrow {
                        bottom: -11px;
                        left: 50%;
                        margin-left: -11px;
                        border-top-color: #999;
                        border-top-color: rgba(0, 0, 0, 0.25);
                        border-bottom-width: 0;
                        }
                        .popover.top > .arrow:after {
                        bottom: 1px;
                        margin-left: -10px;
                        content: " ";
                        border-top-color: #fff;
                        border-bottom-width: 0;
                        }
                        .popover.right > .arrow {
                        top: 50%;
                        left: -11px;
                        margin-top: -11px;
                        border-right-color: #999;
                        border-right-color: rgba(0, 0, 0, 0.25);
                        border-left-width: 0;
                        }
                        .popover.right > .arrow:after {
                        bottom: -10px;
                        left: 1px;
                        content: " ";
                        border-right-color: #fff;
                        border-left-width: 0;
                        }
                        .popover.bottom > .arrow {
                        top: -11px;
                        left: 50%;
                        margin-left: -11px;
                        border-top-width: 0;
                        border-bottom-color: #999;
                        border-bottom-color: rgba(0, 0, 0, 0.25);
                        }
                        .popover.bottom > .arrow:after {
                        top: 1px;
                        margin-left: -10px;
                        content: " ";
                        border-top-width: 0;
                        border-bottom-color: #fff;
                        }
                        .popover.left > .arrow {
                        top: 50%;
                        right: -11px;
                        margin-top: -11px;
                        border-right-width: 0;
                        border-left-color: #999;
                        border-left-color: rgba(0, 0, 0, 0.25);
                        }
                        .popover.left > .arrow:after {
                        right: 1px;
                        bottom: -10px;
                        content: " ";
                        border-right-width: 0;
                        border-left-color: #fff;
                        }
                        .carousel {
                        position: relative;
                        }
                        .carousel-inner {
                        position: relative;
                        width: 100%;
                        overflow: hidden;
                        }
                        .carousel-inner > .item {
                        position: relative;
                        display: none;
                        -webkit-transition: 0.6s ease-in-out left;
                        -o-transition: 0.6s ease-in-out left;
                        transition: 0.6s ease-in-out left;
                        }
                        .carousel-inner > .item > a > img,
                        .carousel-inner > .item > img {
                        line-height: 1;
                        }
                        @media all and (transform-3d), (-webkit-transform-3d) {
                        .carousel-inner > .item {
                            -webkit-transition: -webkit-transform 0.6s ease-in-out;
                            -o-transition: -o-transform 0.6s ease-in-out;
                            transition: transform 0.6s ease-in-out;
                            -webkit-backface-visibility: hidden;
                            backface-visibility: hidden;
                            -webkit-perspective: 1000px;
                            perspective: 1000px;
                        }
                        .carousel-inner > .item.active.right,
                        .carousel-inner > .item.next {
                            left: 0;
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                        }
                        .carousel-inner > .item.active.left,
                        .carousel-inner > .item.prev {
                            left: 0;
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                        }
                        .carousel-inner > .item.active,
                        .carousel-inner > .item.next.left,
                        .carousel-inner > .item.prev.right {
                            left: 0;
                            -webkit-transform: translate3d(0, 0, 0);
                            transform: translate3d(0, 0, 0);
                        }
                        }
                        .carousel-inner > .active,
                        .carousel-inner > .next,
                        .carousel-inner > .prev {
                        display: block;
                        }
                        .carousel-inner > .active {
                        left: 0;
                        }
                        .carousel-inner > .next,
                        .carousel-inner > .prev {
                        position: absolute;
                        top: 0;
                        width: 100%;
                        }
                        .carousel-inner > .next {
                        left: 100%;
                        }
                        .carousel-inner > .prev {
                        left: -100%;
                        }
                        .carousel-inner > .next.left,
                        .carousel-inner > .prev.right {
                        left: 0;
                        }
                        .carousel-inner > .active.left {
                        left: -100%;
                        }
                        .carousel-inner > .active.right {
                        left: 100%;
                        }
                        .carousel-control {
                        position: absolute;
                        top: 0;
                        bottom: 0;
                        left: 0;
                        width: 15%;
                        font-size: 20px;
                        color: #fff;
                        text-align: center;
                        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
                        background-color: rgba(0, 0, 0, 0);
                        opacity: 0.5;
                        }
                        .carousel-control.left {
                        background-image: -webkit-linear-gradient(
                            left,
                            rgba(0, 0, 0, 0.5) 0,
                            rgba(0, 0, 0, 0.0001) 100%
                        );
                        background-image: -o-linear-gradient(
                            left,
                            rgba(0, 0, 0, 0.5) 0,
                            rgba(0, 0, 0, 0.0001) 100%
                        );
                        background-image: -webkit-gradient(
                            linear,
                            left top,
                            right top,
                            from(rgba(0, 0, 0, 0.5)),
                            to(rgba(0, 0, 0, 0.0001))
                        );
                        background-image: linear-gradient(
                            to right,
                            rgba(0, 0, 0, 0.5) 0,
                            rgba(0, 0, 0, 0.0001) 100%
                        );
                        background-repeat: repeat-x;
                        }
                        .carousel-control.right {
                        right: 0;
                        left: auto;
                        background-image: -webkit-linear-gradient(
                            left,
                            rgba(0, 0, 0, 0.0001) 0,
                            rgba(0, 0, 0, 0.5) 100%
                        );
                        background-image: -o-linear-gradient(
                            left,
                            rgba(0, 0, 0, 0.0001) 0,
                            rgba(0, 0, 0, 0.5) 100%
                        );
                        background-image: -webkit-gradient(
                            linear,
                            left top,
                            right top,
                            from(rgba(0, 0, 0, 0.0001)),
                            to(rgba(0, 0, 0, 0.5))
                        );
                        background-image: linear-gradient(
                            to right,
                            rgba(0, 0, 0, 0.0001) 0,
                            rgba(0, 0, 0, 0.5) 100%
                        );
                        background-repeat: repeat-x;
                        }
                        .carousel-control:focus,
                        .carousel-control:hover {
                        color: #fff;
                        text-decoration: none;
                        outline: 0;
                        opacity: 0.9;
                        }
                        .carousel-control .glyphicon-chevron-left,
                        .carousel-control .glyphicon-chevron-right,
                        .carousel-control .icon-next,
                        .carousel-control .icon-prev {
                        position: absolute;
                        top: 50%;
                        z-index: 5;
                        display: inline-block;
                        margin-top: -10px;
                        }
                        .carousel-control .glyphicon-chevron-left,
                        .carousel-control .icon-prev {
                        left: 50%;
                        margin-left: -10px;
                        }
                        .carousel-control .glyphicon-chevron-right,
                        .carousel-control .icon-next {
                        right: 50%;
                        margin-right: -10px;
                        }
                        .carousel-control .icon-next,
                        .carousel-control .icon-prev {
                        width: 20px;
                        height: 20px;
                        font-family: serif;
                        line-height: 1;
                        }
                        .carousel-control .icon-prev:before {
                        content: "\2039";
                        }
                        .carousel-control .icon-next:before {
                        content: "\203a";
                        }
                        .carousel-indicators {
                        position: absolute;
                        bottom: 10px;
                        left: 50%;
                        z-index: 15;
                        width: 60%;
                        padding-left: 0;
                        margin-left: -30%;
                        text-align: center;
                        list-style: none;
                        }
                        .carousel-indicators li {
                        display: inline-block;
                        width: 10px;
                        height: 10px;
                        margin: 1px;
                        text-indent: -999px;
                        cursor: pointer;
                        background-color: rgba(0, 0, 0, 0);
                        border: 1px solid #fff;
                        border-radius: 10px;
                        }
                        .carousel-indicators .active {
                        width: 12px;
                        height: 12px;
                        margin: 0;
                        background-color: #fff;
                        }
                        .carousel-caption {
                        position: absolute;
                        right: 15%;
                        bottom: 20px;
                        left: 15%;
                        z-index: 10;
                        padding-top: 20px;
                        padding-bottom: 20px;
                        color: #fff;
                        text-align: center;
                        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
                        }
                        .carousel-caption .btn {
                        text-shadow: none;
                        }
                        @media screen and (min-width: 768px) {
                        .carousel-control .glyphicon-chevron-left,
                        .carousel-control .glyphicon-chevron-right,
                        .carousel-control .icon-next,
                        .carousel-control .icon-prev {
                            width: 30px;
                            height: 30px;
                            margin-top: -10px;
                            font-size: 30px;
                        }
                        .carousel-control .glyphicon-chevron-left,
                        .carousel-control .icon-prev {
                            margin-left: -10px;
                        }
                        .carousel-control .glyphicon-chevron-right,
                        .carousel-control .icon-next {
                            margin-right: -10px;
                        }
                        .carousel-caption {
                            right: 20%;
                            left: 20%;
                            padding-bottom: 30px;
                        }
                        .carousel-indicators {
                            bottom: 20px;
                        }
                        }
                        .btn-group-vertical > .btn-group:after,
                        .btn-group-vertical > .btn-group:before,
                        .btn-toolbar:after,
                        .btn-toolbar:before,
                        .clearfix:after,
                        .clearfix:before,
                        .container-fluid:after,
                        .container-fluid:before,
                        .container:after,
                        .container:before,
                        .dl-horizontal dd:after,
                        .dl-horizontal dd:before,
                        .form-horizontal .form-group:after,
                        .form-horizontal .form-group:before,
                        .modal-footer:after,
                        .modal-footer:before,
                        .modal-header:after,
                        .modal-header:before,
                        .nav:after,
                        .nav:before,
                        .navbar-collapse:after,
                        .navbar-collapse:before,
                        .navbar-header:after,
                        .navbar-header:before,
                        .navbar:after,
                        .navbar:before,
                        .pager:after,
                        .pager:before,
                        .panel-body:after,
                        .panel-body:before,
                        .row:after,
                        .row:before {
                        display: table;
                        content: " ";
                        }
                        .btn-group-vertical > .btn-group:after,
                        .btn-toolbar:after,
                        .clearfix:after,
                        .container-fluid:after,
                        .container:after,
                        .dl-horizontal dd:after,
                        .form-horizontal .form-group:after,
                        .modal-footer:after,
                        .modal-header:after,
                        .nav:after,
                        .navbar-collapse:after,
                        .navbar-header:after,
                        .navbar:after,
                        .pager:after,
                        .panel-body:after,
                        .row:after {
                        clear: both;
                        }
                        .center-block {
                        display: block;
                        margin-right: auto;
                        margin-left: auto;
                        }
                        .pull-right {
                        float: right !important;
                        }
                        .pull-left {
                        float: left !important;
                        }
                        .hide {
                        display: none !important;
                        }
                        .show {
                        display: block !important;
                        }
                        .invisible {
                        visibility: hidden;
                        }
                        .text-hide {
                        font: 0/0 a;
                        color: transparent;
                        text-shadow: none;
                        background-color: transparent;
                        border: 0;
                        }
                        .hidden {
                        display: none !important;
                        }
                        .affix {
                        position: fixed;
                        }
                        @-ms-viewport {
                        width: device-width;
                        }
                        .visible-lg,
                        .visible-md,
                        .visible-sm,
                        .visible-xs {
                        display: none !important;
                        }
                        .visible-lg-block,
                        .visible-lg-inline,
                        .visible-lg-inline-block,
                        .visible-md-block,
                        .visible-md-inline,
                        .visible-md-inline-block,
                        .visible-sm-block,
                        .visible-sm-inline,
                        .visible-sm-inline-block,
                        .visible-xs-block,
                        .visible-xs-inline,
                        .visible-xs-inline-block {
                        display: none !important;
                        }
                        @media (max-width: 767px) {
                        .visible-xs {
                            display: block !important;
                        }
                        table.visible-xs {
                            display: table !important;
                        }
                        tr.visible-xs {
                            display: table-row !important;
                        }
                        td.visible-xs,
                        th.visible-xs {
                            display: table-cell !important;
                        }
                        }
                        @media (max-width: 767px) {
                        .visible-xs-block {
                            display: block !important;
                        }
                        }
                        @media (max-width: 767px) {
                        .visible-xs-inline {
                            display: inline !important;
                        }
                        }
                        @media (max-width: 767px) {
                        .visible-xs-inline-block {
                            display: inline-block !important;
                        }
                        }
                        @media (min-width: 768px) and (max-width: 991px) {
                        .visible-sm {
                            display: block !important;
                        }
                        table.visible-sm {
                            display: table !important;
                        }
                        tr.visible-sm {
                            display: table-row !important;
                        }
                        td.visible-sm,
                        th.visible-sm {
                            display: table-cell !important;
                        }
                        }
                        @media (min-width: 768px) and (max-width: 991px) {
                        .visible-sm-block {
                            display: block !important;
                        }
                        }
                        @media (min-width: 768px) and (max-width: 991px) {
                        .visible-sm-inline {
                            display: inline !important;
                        }
                        }
                        @media (min-width: 768px) and (max-width: 991px) {
                        .visible-sm-inline-block {
                            display: inline-block !important;
                        }
                        }
                        @media (min-width: 992px) and (max-width: 1199px) {
                        .visible-md {
                            display: block !important;
                        }
                        table.visible-md {
                            display: table !important;
                        }
                        tr.visible-md {
                            display: table-row !important;
                        }
                        td.visible-md,
                        th.visible-md {
                            display: table-cell !important;
                        }
                        }
                        @media (min-width: 992px) and (max-width: 1199px) {
                        .visible-md-block {
                            display: block !important;
                        }
                        }
                        @media (min-width: 992px) and (max-width: 1199px) {
                        .visible-md-inline {
                            display: inline !important;
                        }
                        }
                        @media (min-width: 992px) and (max-width: 1199px) {
                        .visible-md-inline-block {
                            display: inline-block !important;
                        }
                        }
                        @media (min-width: 1200px) {
                        .visible-lg {
                            display: block !important;
                        }
                        table.visible-lg {
                            display: table !important;
                        }
                        tr.visible-lg {
                            display: table-row !important;
                        }
                        td.visible-lg,
                        th.visible-lg {
                            display: table-cell !important;
                        }
                        }
                        @media (min-width: 1200px) {
                        .visible-lg-block {
                            display: block !important;
                        }
                        }
                        @media (min-width: 1200px) {
                        .visible-lg-inline {
                            display: inline !important;
                        }
                        }
                        @media (min-width: 1200px) {
                        .visible-lg-inline-block {
                            display: inline-block !important;
                        }
                        }
                        @media (max-width: 767px) {
                        .hidden-xs {
                            display: none !important;
                        }
                        }
                        @media (min-width: 768px) and (max-width: 991px) {
                        .hidden-sm {
                            display: none !important;
                        }
                        }
                        @media (min-width: 992px) and (max-width: 1199px) {
                        .hidden-md {
                            display: none !important;
                        }
                        }
                        @media (min-width: 1200px) {
                        .hidden-lg {
                            display: none !important;
                        }
                        }
                        .visible-print {
                        display: none !important;
                        }
                        @media print {
                        .visible-print {
                            display: block !important;
                        }
                        table.visible-print {
                            display: table !important;
                        }
                        tr.visible-print {
                            display: table-row !important;
                        }
                        td.visible-print,
                        th.visible-print {
                            display: table-cell !important;
                        }
                        }
                        .visible-print-block {
                        display: none !important;
                        }
                        @media print {
                        .visible-print-block {
                            display: block !important;
                        }
                        }
                        .visible-print-inline {
                        display: none !important;
                        }
                        @media print {
                        .visible-print-inline {
                            display: inline !important;
                        }
                        }
                        .visible-print-inline-block {
                        display: none !important;
                        }
                        @media print {
                        .visible-print-inline-block {
                            display: inline-block !important;
                        }
                        }
                        @media print {
                        .hidden-print {
                            display: none !important;
                        }
                        } /*! jQuery UI - v1.12.1 - 2016-09-14
                        * http://jqueryui.com
                        * Includes: core.css, accordion.css, autocomplete.css, menu.css, button.css, controlgroup.css, checkboxradio.css, datepicker.css, dialog.css, draggable.css, resizable.css, progressbar.css, selectable.css, selectmenu.css, slider.css, sortable.css, spinner.css, tabs.css, tooltip.css, theme.css
                        * To view and modify this theme, visit http://jqueryui.com/themeroller/?bgShadowXPos=&bgOverlayXPos=&bgErrorXPos=&bgHighlightXPos=&bgContentXPos=&bgHeaderXPos=&bgActiveXPos=&bgHoverXPos=&bgDefaultXPos=&bgShadowYPos=&bgOverlayYPos=&bgErrorYPos=&bgHighlightYPos=&bgContentYPos=&bgHeaderYPos=&bgActiveYPos=&bgHoverYPos=&bgDefaultYPos=&bgShadowRepeat=&bgOverlayRepeat=&bgErrorRepeat=&bgHighlightRepeat=&bgContentRepeat=&bgHeaderRepeat=&bgActiveRepeat=&bgHoverRepeat=&bgDefaultRepeat=&iconsHover=url(%22images%2Fui-icons_555555_256x240.png%22)&iconsHighlight=url(%22images%2Fui-icons_777620_256x240.png%22)&iconsHeader=url(%22images%2Fui-icons_444444_256x240.png%22)&iconsError=url(%22images%2Fui-icons_cc0000_256x240.png%22)&iconsDefault=url(%22images%2Fui-icons_777777_256x240.png%22)&iconsContent=url(%22images%2Fui-icons_444444_256x240.png%22)&iconsActive=url(%22images%2Fui-icons_ffffff_256x240.png%22)&bgImgUrlShadow=&bgImgUrlOverlay=&bgImgUrlHover=&bgImgUrlHighlight=&bgImgUrlHeader=&bgImgUrlError=&bgImgUrlDefault=&bgImgUrlContent=&bgImgUrlActive=&opacityFilterShadow=Alpha(Opacity%3D30)&opacityFilterOverlay=Alpha(Opacity%3D30)&opacityShadowPerc=30&opacityOverlayPerc=30&iconColorHover=%23555555&iconColorHighlight=%23777620&iconColorHeader=%23444444&iconColorError=%23cc0000&iconColorDefault=%23777777&iconColorContent=%23444444&iconColorActive=%23ffffff&bgImgOpacityShadow=0&bgImgOpacityOverlay=0&bgImgOpacityError=95&bgImgOpacityHighlight=55&bgImgOpacityContent=75&bgImgOpacityHeader=75&bgImgOpacityActive=65&bgImgOpacityHover=75&bgImgOpacityDefault=75&bgTextureShadow=flat&bgTextureOverlay=flat&bgTextureError=flat&bgTextureHighlight=flat&bgTextureContent=flat&bgTextureHeader=flat&bgTextureActive=flat&bgTextureHover=flat&bgTextureDefault=flat&cornerRadius=3px&fwDefault=normal&ffDefault=Arial%2CHelvetica%2Csans-serif&fsDefault=1em&cornerRadiusShadow=8px&thicknessShadow=5px&offsetLeftShadow=0px&offsetTopShadow=0px&opacityShadow=.3&bgColorShadow=%23666666&opacityOverlay=.3&bgColorOverlay=%23aaaaaa&fcError=%235f3f3f&borderColorError=%23f1a899&bgColorError=%23fddfdf&fcHighlight=%23777620&borderColorHighlight=%23dad55e&bgColorHighlight=%23fffa90&fcContent=%23333333&borderColorContent=%23dddddd&bgColorContent=%23ffffff&fcHeader=%23333333&borderColorHeader=%23dddddd&bgColorHeader=%23e9e9e9&fcActive=%23ffffff&borderColorActive=%23003eff&bgColorActive=%23007fff&fcHover=%232b2b2b&borderColorHover=%23cccccc&bgColorHover=%23ededed&fcDefault=%23454545&borderColorDefault=%23c5c5c5&bgColorDefault=%23f6f6f6
                        * Copyright jQuery Foundation and other contributors; Licensed MIT */
                        .ui-helper-hidden {
                        display: none;
                        }
                        .ui-helper-hidden-accessible {
                        border: 0;
                        clip: rect(0 0 0 0);
                        height: 1px;
                        margin: -1px;
                        overflow: hidden;
                        padding: 0;
                        position: absolute;
                        width: 1px;
                        }
                        .ui-helper-reset {
                        margin: 0;
                        padding: 0;
                        border: 0;
                        outline: 0;
                        line-height: 1.3;
                        text-decoration: none;
                        font-size: 100%;
                        list-style: none;
                        }
                        .ui-helper-clearfix:after,
                        .ui-helper-clearfix:before {
                        content: "";
                        display: table;
                        border-collapse: collapse;
                        }
                        .ui-helper-clearfix:after {
                        clear: both;
                        }
                        .ui-helper-zfix {
                        width: 100%;
                        height: 100%;
                        top: 0;
                        left: 0;
                        position: absolute;
                        opacity: 0;
                        filter: Alpha(Opacity=0);
                        }
                        .ui-front {
                        z-index: 100;
                        }
                        .ui-state-disabled {
                        cursor: default !important;
                        pointer-events: none;
                        }
                        .ui-icon {
                        display: inline-block;
                        vertical-align: middle;
                        margin-top: -0.25em;
                        position: relative;
                        text-indent: -99999px;
                        overflow: hidden;
                        background-repeat: no-repeat;
                        }
                        .ui-widget-icon-block {
                        left: 50%;
                        margin-left: -8px;
                        display: block;
                        }
                        .ui-widget-overlay {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        }
                        .ui-accordion .ui-accordion-header {
                        display: block;
                        cursor: pointer;
                        position: relative;
                        margin: 2px 0 0 0;
                        padding: 0.5em 0.5em 0.5em 0.7em;
                        font-size: 100%;
                        }
                        .ui-accordion .ui-accordion-content {
                        padding: 1em 2.2em;
                        border-top: 0;
                        overflow: auto;
                        }
                        .ui-autocomplete {
                        position: absolute;
                        top: 0;
                        left: 0;
                        cursor: default;
                        }
                        .ui-menu {
                        list-style: none;
                        padding: 0;
                        margin: 0;
                        display: block;
                        outline: 0;
                        }
                        .ui-menu .ui-menu {
                        position: absolute;
                        }
                        .ui-menu .ui-menu-item {
                        margin: 0;
                        cursor: pointer;
                        list-style-image: url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7);
                        }
                        .ui-menu .ui-menu-item-wrapper {
                        position: relative;
                        padding: 3px 1em 3px 0.4em;
                        }
                        .ui-menu .ui-menu-divider {
                        margin: 5px 0;
                        height: 0;
                        font-size: 0;
                        line-height: 0;
                        border-width: 1px 0 0 0;
                        }
                        .ui-menu .ui-state-active,
                        .ui-menu .ui-state-focus {
                        margin: -1px;
                        }
                        .ui-menu-icons {
                        position: relative;
                        }
                        .ui-menu-icons .ui-menu-item-wrapper {
                        padding-left: 2em;
                        }
                        .ui-menu .ui-icon {
                        position: absolute;
                        top: 0;
                        bottom: 0;
                        left: 0.2em;
                        margin: auto 0;
                        }
                        .ui-menu .ui-menu-icon {
                        left: auto;
                        right: 0;
                        }
                        .ui-button {
                        padding: 0.4em 1em;
                        display: inline-block;
                        position: relative;
                        line-height: normal;
                        margin-right: 0.1em;
                        cursor: pointer;
                        vertical-align: middle;
                        text-align: center;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        user-select: none;
                        overflow: visible;
                        }
                        .ui-button,
                        .ui-button:active,
                        .ui-button:hover,
                        .ui-button:link,
                        .ui-button:visited {
                        text-decoration: none;
                        }
                        .ui-button-icon-only {
                        width: 2em;
                        box-sizing: border-box;
                        text-indent: -9999px;
                        white-space: nowrap;
                        }
                        input.ui-button.ui-button-icon-only {
                        text-indent: 0;
                        }
                        .ui-button-icon-only .ui-icon {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        margin-top: -8px;
                        margin-left: -8px;
                        }
                        .ui-button.ui-icon-notext .ui-icon {
                        padding: 0;
                        width: 2.1em;
                        height: 2.1em;
                        text-indent: -9999px;
                        white-space: nowrap;
                        }
                        input.ui-button.ui-icon-notext .ui-icon {
                        width: auto;
                        height: auto;
                        text-indent: 0;
                        white-space: normal;
                        padding: 0.4em 1em;
                        }
                        button.ui-button::-moz-focus-inner,
                        input.ui-button::-moz-focus-inner {
                        border: 0;
                        padding: 0;
                        }
                        .ui-controlgroup {
                        vertical-align: middle;
                        display: inline-block;
                        }
                        .ui-controlgroup > .ui-controlgroup-item {
                        float: left;
                        margin-left: 0;
                        margin-right: 0;
                        }
                        .ui-controlgroup > .ui-controlgroup-item.ui-visual-focus,
                        .ui-controlgroup > .ui-controlgroup-item:focus {
                        z-index: 9999;
                        }
                        .ui-controlgroup-vertical > .ui-controlgroup-item {
                        display: block;
                        float: none;
                        width: 100%;
                        margin-top: 0;
                        margin-bottom: 0;
                        text-align: left;
                        }
                        .ui-controlgroup-vertical .ui-controlgroup-item {
                        box-sizing: border-box;
                        }
                        .ui-controlgroup .ui-controlgroup-label {
                        padding: 0.4em 1em;
                        }
                        .ui-controlgroup .ui-controlgroup-label span {
                        font-size: 80%;
                        }
                        .ui-controlgroup-horizontal .ui-controlgroup-label + .ui-controlgroup-item {
                        border-left: none;
                        }
                        .ui-controlgroup-vertical .ui-controlgroup-label + .ui-controlgroup-item {
                        border-top: none;
                        }
                        .ui-controlgroup-horizontal .ui-controlgroup-label.ui-widget-content {
                        border-right: none;
                        }
                        .ui-controlgroup-vertical .ui-controlgroup-label.ui-widget-content {
                        border-bottom: none;
                        }
                        .ui-controlgroup-vertical .ui-spinner-input {
                        width: 75%;
                        width: calc(100% - 2.4em);
                        }
                        .ui-controlgroup-vertical .ui-spinner .ui-spinner-up {
                        border-top-style: solid;
                        }
                        .ui-checkboxradio-label .ui-icon-background {
                        box-shadow: inset 1px 1px 1px #ccc;
                        border-radius: 0.12em;
                        border: none;
                        }
                        .ui-checkboxradio-radio-label .ui-icon-background {
                        width: 16px;
                        height: 16px;
                        border-radius: 1em;
                        overflow: visible;
                        border: none;
                        }
                        .ui-checkboxradio-radio-label.ui-checkboxradio-checked .ui-icon,
                        .ui-checkboxradio-radio-label.ui-checkboxradio-checked:hover .ui-icon {
                        background-image: none;
                        width: 8px;
                        height: 8px;
                        border-width: 4px;
                        border-style: solid;
                        }
                        .ui-checkboxradio-disabled {
                        pointer-events: none;
                        }
                        .ui-datepicker {
                        width: 17em;
                        padding: 0.2em 0.2em 0;
                        display: none;
                        }
                        .ui-datepicker .ui-datepicker-header {
                        position: relative;
                        padding: 0.2em 0;
                        }
                        .ui-datepicker .ui-datepicker-next,
                        .ui-datepicker .ui-datepicker-prev {
                        position: absolute;
                        top: 2px;
                        width: 1.8em;
                        height: 1.8em;
                        }
                        .ui-datepicker .ui-datepicker-next-hover,
                        .ui-datepicker .ui-datepicker-prev-hover {
                        top: 1px;
                        }
                        .ui-datepicker .ui-datepicker-prev {
                        left: 2px;
                        }
                        .ui-datepicker .ui-datepicker-next {
                        right: 2px;
                        }
                        .ui-datepicker .ui-datepicker-prev-hover {
                        left: 1px;
                        }
                        .ui-datepicker .ui-datepicker-next-hover {
                        right: 1px;
                        }
                        .ui-datepicker .ui-datepicker-next span,
                        .ui-datepicker .ui-datepicker-prev span {
                        display: block;
                        position: absolute;
                        left: 50%;
                        margin-left: -8px;
                        top: 50%;
                        margin-top: -8px;
                        }
                        .ui-datepicker .ui-datepicker-title {
                        margin: 0 2.3em;
                        line-height: 1.8em;
                        text-align: center;
                        }
                        .ui-datepicker .ui-datepicker-title select {
                        font-size: 1em;
                        margin: 1px 0;
                        }
                        .ui-datepicker select.ui-datepicker-month,
                        .ui-datepicker select.ui-datepicker-year {
                        width: 45%;
                        }
                        .ui-datepicker table {
                        width: 100%;
                        font-size: 0.9em;
                        border-collapse: collapse;
                        margin: 0 0 0.4em;
                        }
                        .ui-datepicker th {
                        padding: 0.7em 0.3em;
                        text-align: center;
                        font-weight: 700;
                        border: 0;
                        }
                        .ui-datepicker td {
                        border: 0;
                        padding: 1px;
                        }
                        .ui-datepicker td a,
                        .ui-datepicker td span {
                        display: block;
                        padding: 0.2em;
                        text-align: right;
                        text-decoration: none;
                        }
                        .ui-datepicker .ui-datepicker-buttonpane {
                        background-image: none;
                        margin: 0.7em 0 0 0;
                        padding: 0 0.2em;
                        border-left: 0;
                        border-right: 0;
                        border-bottom: 0;
                        }
                        .ui-datepicker .ui-datepicker-buttonpane button {
                        float: right;
                        margin: 0.5em 0.2em 0.4em;
                        cursor: pointer;
                        padding: 0.2em 0.6em 0.3em 0.6em;
                        width: auto;
                        overflow: visible;
                        }
                        .ui-datepicker .ui-datepicker-buttonpane button.ui-datepicker-current {
                        float: left;
                        }
                        .ui-datepicker.ui-datepicker-multi {
                        width: auto;
                        }
                        .ui-datepicker-multi .ui-datepicker-group {
                        float: left;
                        }
                        .ui-datepicker-multi .ui-datepicker-group table {
                        width: 95%;
                        margin: 0 auto 0.4em;
                        }
                        .ui-datepicker-multi-2 .ui-datepicker-group {
                        width: 50%;
                        }
                        .ui-datepicker-multi-3 .ui-datepicker-group {
                        width: 33.3%;
                        }
                        .ui-datepicker-multi-4 .ui-datepicker-group {
                        width: 25%;
                        }
                        .ui-datepicker-multi .ui-datepicker-group-last .ui-datepicker-header,
                        .ui-datepicker-multi .ui-datepicker-group-middle .ui-datepicker-header {
                        border-left-width: 0;
                        }
                        .ui-datepicker-multi .ui-datepicker-buttonpane {
                        clear: left;
                        }
                        .ui-datepicker-row-break {
                        clear: both;
                        width: 100%;
                        font-size: 0;
                        }
                        .ui-datepicker-rtl {
                        direction: rtl;
                        }
                        .ui-datepicker-rtl .ui-datepicker-prev {
                        right: 2px;
                        left: auto;
                        }
                        .ui-datepicker-rtl .ui-datepicker-next {
                        left: 2px;
                        right: auto;
                        }
                        .ui-datepicker-rtl .ui-datepicker-prev:hover {
                        right: 1px;
                        left: auto;
                        }
                        .ui-datepicker-rtl .ui-datepicker-next:hover {
                        left: 1px;
                        right: auto;
                        }
                        .ui-datepicker-rtl .ui-datepicker-buttonpane {
                        clear: right;
                        }
                        .ui-datepicker-rtl .ui-datepicker-buttonpane button {
                        float: left;
                        }
                        .ui-datepicker-rtl .ui-datepicker-buttonpane button.ui-datepicker-current,
                        .ui-datepicker-rtl .ui-datepicker-group {
                        float: right;
                        }
                        .ui-datepicker-rtl .ui-datepicker-group-last .ui-datepicker-header,
                        .ui-datepicker-rtl .ui-datepicker-group-middle .ui-datepicker-header {
                        border-right-width: 0;
                        border-left-width: 1px;
                        }
                        .ui-datepicker .ui-icon {
                        display: block;
                        text-indent: -99999px;
                        overflow: hidden;
                        background-repeat: no-repeat;
                        left: 0.5em;
                        top: 0.3em;
                        }
                        .ui-dialog {
                        position: absolute;
                        top: 0;
                        left: 0;
                        padding: 0.2em;
                        outline: 0;
                        }
                        .ui-dialog .ui-dialog-titlebar {
                        padding: 0.4em 1em;
                        position: relative;
                        }
                        .ui-dialog .ui-dialog-title {
                        float: left;
                        margin: 0.1em 0;
                        white-space: nowrap;
                        width: 90%;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        }
                        .ui-dialog .ui-dialog-titlebar-close {
                        position: absolute;
                        right: 0.3em;
                        top: 50%;
                        width: 20px;
                        margin: -10px 0 0 0;
                        padding: 1px;
                        height: 20px;
                        }
                        .ui-dialog .ui-dialog-content {
                        position: relative;
                        border: 0;
                        padding: 0.5em 1em;
                        background: 0 0;
                        overflow: auto;
                        }
                        .ui-dialog .ui-dialog-buttonpane {
                        text-align: left;
                        border-width: 1px 0 0 0;
                        background-image: none;
                        margin-top: 0.5em;
                        padding: 0.3em 1em 0.5em 0.4em;
                        }
                        .ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset {
                        float: right;
                        }
                        .ui-dialog .ui-dialog-buttonpane button {
                        margin: 0.5em 0.4em 0.5em 0;
                        cursor: pointer;
                        }
                        .ui-dialog .ui-resizable-n {
                        height: 2px;
                        top: 0;
                        }
                        .ui-dialog .ui-resizable-e {
                        width: 2px;
                        right: 0;
                        }
                        .ui-dialog .ui-resizable-s {
                        height: 2px;
                        bottom: 0;
                        }
                        .ui-dialog .ui-resizable-w {
                        width: 2px;
                        left: 0;
                        }
                        .ui-dialog .ui-resizable-ne,
                        .ui-dialog .ui-resizable-nw,
                        .ui-dialog .ui-resizable-se,
                        .ui-dialog .ui-resizable-sw {
                        width: 7px;
                        height: 7px;
                        }
                        .ui-dialog .ui-resizable-se {
                        right: 0;
                        bottom: 0;
                        }
                        .ui-dialog .ui-resizable-sw {
                        left: 0;
                        bottom: 0;
                        }
                        .ui-dialog .ui-resizable-ne {
                        right: 0;
                        top: 0;
                        }
                        .ui-dialog .ui-resizable-nw {
                        left: 0;
                        top: 0;
                        }
                        .ui-draggable .ui-dialog-titlebar {
                        cursor: move;
                        }
                        .ui-draggable-handle {
                        -ms-touch-action: none;
                        touch-action: none;
                        }
                        .ui-resizable {
                        position: relative;
                        }
                        .ui-resizable-handle {
                        position: absolute;
                        font-size: 0.1px;
                        display: block;
                        -ms-touch-action: none;
                        touch-action: none;
                        }
                        .ui-resizable-autohide .ui-resizable-handle,
                        .ui-resizable-disabled .ui-resizable-handle {
                        display: none;
                        }
                        .ui-resizable-n {
                        cursor: n-resize;
                        height: 7px;
                        width: 100%;
                        top: -5px;
                        left: 0;
                        }
                        .ui-resizable-s {
                        cursor: s-resize;
                        height: 7px;
                        width: 100%;
                        bottom: -5px;
                        left: 0;
                        }
                        .ui-resizable-e {
                        cursor: e-resize;
                        width: 7px;
                        right: -5px;
                        top: 0;
                        height: 100%;
                        }
                        .ui-resizable-w {
                        cursor: w-resize;
                        width: 7px;
                        left: -5px;
                        top: 0;
                        height: 100%;
                        }
                        .ui-resizable-se {
                        cursor: se-resize;
                        width: 12px;
                        height: 12px;
                        right: 1px;
                        bottom: 1px;
                        }
                        .ui-resizable-sw {
                        cursor: sw-resize;
                        width: 9px;
                        height: 9px;
                        left: -5px;
                        bottom: -5px;
                        }
                        .ui-resizable-nw {
                        cursor: nw-resize;
                        width: 9px;
                        height: 9px;
                        left: -5px;
                        top: -5px;
                        }
                        .ui-resizable-ne {
                        cursor: ne-resize;
                        width: 9px;
                        height: 9px;
                        right: -5px;
                        top: -5px;
                        }
                        .ui-progressbar {
                        height: 2em;
                        text-align: left;
                        overflow: hidden;
                        }
                        .ui-progressbar .ui-progressbar-value {
                        margin: -1px;
                        height: 100%;
                        }
                        .ui-progressbar .ui-progressbar-overlay {
                        background: url(data:image/gif;base64,R0lGODlhKAAoAIABAAAAAP///yH/C05FVFNDQVBFMi4wAwEAAAAh+QQJAQABACwAAAAAKAAoAAACkYwNqXrdC52DS06a7MFZI+4FHBCKoDeWKXqymPqGqxvJrXZbMx7Ttc+w9XgU2FB3lOyQRWET2IFGiU9m1frDVpxZZc6bfHwv4c1YXP6k1Vdy292Fb6UkuvFtXpvWSzA+HycXJHUXiGYIiMg2R6W459gnWGfHNdjIqDWVqemH2ekpObkpOlppWUqZiqr6edqqWQAAIfkECQEAAQAsAAAAACgAKAAAApSMgZnGfaqcg1E2uuzDmmHUBR8Qil95hiPKqWn3aqtLsS18y7G1SzNeowWBENtQd+T1JktP05nzPTdJZlR6vUxNWWjV+vUWhWNkWFwxl9VpZRedYcflIOLafaa28XdsH/ynlcc1uPVDZxQIR0K25+cICCmoqCe5mGhZOfeYSUh5yJcJyrkZWWpaR8doJ2o4NYq62lAAACH5BAkBAAEALAAAAAAoACgAAAKVDI4Yy22ZnINRNqosw0Bv7i1gyHUkFj7oSaWlu3ovC8GxNso5fluz3qLVhBVeT/Lz7ZTHyxL5dDalQWPVOsQWtRnuwXaFTj9jVVh8pma9JjZ4zYSj5ZOyma7uuolffh+IR5aW97cHuBUXKGKXlKjn+DiHWMcYJah4N0lYCMlJOXipGRr5qdgoSTrqWSq6WFl2ypoaUAAAIfkECQEAAQAsAAAAACgAKAAAApaEb6HLgd/iO7FNWtcFWe+ufODGjRfoiJ2akShbueb0wtI50zm02pbvwfWEMWBQ1zKGlLIhskiEPm9R6vRXxV4ZzWT2yHOGpWMyorblKlNp8HmHEb/lCXjcW7bmtXP8Xt229OVWR1fod2eWqNfHuMjXCPkIGNileOiImVmCOEmoSfn3yXlJWmoHGhqp6ilYuWYpmTqKUgAAIfkECQEAAQAsAAAAACgAKAAAApiEH6kb58biQ3FNWtMFWW3eNVcojuFGfqnZqSebuS06w5V80/X02pKe8zFwP6EFWOT1lDFk8rGERh1TTNOocQ61Hm4Xm2VexUHpzjymViHrFbiELsefVrn6XKfnt2Q9G/+Xdie499XHd2g4h7ioOGhXGJboGAnXSBnoBwKYyfioubZJ2Hn0RuRZaflZOil56Zp6iioKSXpUAAAh+QQJAQABACwAAAAAKAAoAAACkoQRqRvnxuI7kU1a1UU5bd5tnSeOZXhmn5lWK3qNTWvRdQxP8qvaC+/yaYQzXO7BMvaUEmJRd3TsiMAgswmNYrSgZdYrTX6tSHGZO73ezuAw2uxuQ+BbeZfMxsexY35+/Qe4J1inV0g4x3WHuMhIl2jXOKT2Q+VU5fgoSUI52VfZyfkJGkha6jmY+aaYdirq+lQAACH5BAkBAAEALAAAAAAoACgAAAKWBIKpYe0L3YNKToqswUlvznigd4wiR4KhZrKt9Upqip61i9E3vMvxRdHlbEFiEXfk9YARYxOZZD6VQ2pUunBmtRXo1Lf8hMVVcNl8JafV38aM2/Fu5V16Bn63r6xt97j09+MXSFi4BniGFae3hzbH9+hYBzkpuUh5aZmHuanZOZgIuvbGiNeomCnaxxap2upaCZsq+1kAACH5BAkBAAEALAAAAAAoACgAAAKXjI8By5zf4kOxTVrXNVlv1X0d8IGZGKLnNpYtm8Lr9cqVeuOSvfOW79D9aDHizNhDJidFZhNydEahOaDH6nomtJjp1tutKoNWkvA6JqfRVLHU/QUfau9l2x7G54d1fl995xcIGAdXqMfBNadoYrhH+Mg2KBlpVpbluCiXmMnZ2Sh4GBqJ+ckIOqqJ6LmKSllZmsoq6wpQAAAh+QQJAQABACwAAAAAKAAoAAAClYx/oLvoxuJDkU1a1YUZbJ59nSd2ZXhWqbRa2/gF8Gu2DY3iqs7yrq+xBYEkYvFSM8aSSObE+ZgRl1BHFZNr7pRCavZ5BW2142hY3AN/zWtsmf12p9XxxFl2lpLn1rseztfXZjdIWIf2s5dItwjYKBgo9yg5pHgzJXTEeGlZuenpyPmpGQoKOWkYmSpaSnqKileI2FAAACH5BAkBAAEALAAAAAAoACgAAAKVjB+gu+jG4kORTVrVhRlsnn2dJ3ZleFaptFrb+CXmO9OozeL5VfP99HvAWhpiUdcwkpBH3825AwYdU8xTqlLGhtCosArKMpvfa1mMRae9VvWZfeB2XfPkeLmm18lUcBj+p5dnN8jXZ3YIGEhYuOUn45aoCDkp16hl5IjYJvjWKcnoGQpqyPlpOhr3aElaqrq56Bq7VAAAOw==);
                        height: 100%;
                        opacity: 0.25;
                        }
                        .ui-progressbar-indeterminate .ui-progressbar-value {
                        background-image: none;
                        }
                        .ui-selectable {
                        -ms-touch-action: none;
                        touch-action: none;
                        }
                        .ui-selectable-helper {
                        position: absolute;
                        z-index: 100;
                        border: 1px dotted #000;
                        }
                        .ui-selectmenu-menu {
                        padding: 0;
                        margin: 0;
                        position: absolute;
                        top: 0;
                        left: 0;
                        display: none;
                        }
                        .ui-selectmenu-menu .ui-menu {
                        overflow: auto;
                        overflow-x: hidden;
                        padding-bottom: 1px;
                        }
                        .ui-selectmenu-menu .ui-menu .ui-selectmenu-optgroup {
                        font-size: 1em;
                        font-weight: 700;
                        line-height: 1.5;
                        padding: 2px 0.4em;
                        margin: 0.5em 0 0 0;
                        height: auto;
                        border: 0;
                        }
                        .ui-selectmenu-open {
                        display: block;
                        }
                        .ui-selectmenu-text {
                        display: block;
                        margin-right: 20px;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        }
                        .ui-selectmenu-button.ui-button {
                        text-align: left;
                        white-space: nowrap;
                        width: 14em;
                        }
                        .ui-selectmenu-icon.ui-icon {
                        float: right;
                        margin-top: 0;
                        }
                        .ui-slider {
                        position: relative;
                        text-align: left;
                        }
                        .ui-slider .ui-slider-handle {
                        position: absolute;
                        z-index: 2;
                        width: 1.2em;
                        height: 1.2em;
                        cursor: default;
                        -ms-touch-action: none;
                        touch-action: none;
                        }
                        .ui-slider .ui-slider-range {
                        position: absolute;
                        z-index: 1;
                        font-size: 0.7em;
                        display: block;
                        border: 0;
                        background-position: 0 0;
                        }
                        .ui-slider.ui-state-disabled .ui-slider-handle,
                        .ui-slider.ui-state-disabled .ui-slider-range {
                        filter: inherit;
                        }
                        .ui-slider-horizontal {
                        height: 0.8em;
                        }
                        .ui-slider-horizontal .ui-slider-handle {
                        top: -0.3em;
                        margin-left: -0.6em;
                        }
                        .ui-slider-horizontal .ui-slider-range {
                        top: 0;
                        height: 100%;
                        }
                        .ui-slider-horizontal .ui-slider-range-min {
                        left: 0;
                        }
                        .ui-slider-horizontal .ui-slider-range-max {
                        right: 0;
                        }
                        .ui-slider-vertical {
                        width: 0.8em;
                        height: 100px;
                        }
                        .ui-slider-vertical .ui-slider-handle {
                        left: -0.3em;
                        margin-left: 0;
                        margin-bottom: -0.6em;
                        }
                        .ui-slider-vertical .ui-slider-range {
                        left: 0;
                        width: 100%;
                        }
                        .ui-slider-vertical .ui-slider-range-min {
                        bottom: 0;
                        }
                        .ui-slider-vertical .ui-slider-range-max {
                        top: 0;
                        }
                        .ui-sortable-handle {
                        -ms-touch-action: none;
                        touch-action: none;
                        }
                        .ui-spinner {
                        position: relative;
                        display: inline-block;
                        overflow: hidden;
                        padding: 0;
                        vertical-align: middle;
                        }
                        .ui-spinner-input {
                        border: none;
                        background: 0 0;
                        color: inherit;
                        padding: 0.222em 0;
                        margin: 0.2em 0;
                        vertical-align: middle;
                        margin-left: 0.4em;
                        margin-right: 2em;
                        }
                        .ui-spinner-button {
                        width: 1.6em;
                        height: 50%;
                        font-size: 0.5em;
                        padding: 0;
                        margin: 0;
                        text-align: center;
                        position: absolute;
                        cursor: default;
                        display: block;
                        overflow: hidden;
                        right: 0;
                        }
                        .ui-spinner a.ui-spinner-button {
                        border-top-style: none;
                        border-bottom-style: none;
                        border-right-style: none;
                        }
                        .ui-spinner-up {
                        top: 0;
                        }
                        .ui-spinner-down {
                        bottom: 0;
                        }
                        .ui-tabs {
                        position: relative;
                        padding: 0.2em;
                        }
                        .ui-tabs .ui-tabs-nav {
                        margin: 0;
                        padding: 0.2em 0.2em 0;
                        }
                        .ui-tabs .ui-tabs-nav li {
                        list-style: none;
                        float: left;
                        position: relative;
                        top: 0;
                        margin: 1px 0.2em 0 0;
                        border-bottom-width: 0;
                        padding: 0;
                        white-space: nowrap;
                        }
                        .ui-tabs .ui-tabs-nav .ui-tabs-anchor {
                        float: left;
                        padding: 0.5em 1em;
                        text-decoration: none;
                        }
                        .ui-tabs .ui-tabs-nav li.ui-tabs-active {
                        margin-bottom: -1px;
                        padding-bottom: 1px;
                        }
                        .ui-tabs .ui-tabs-nav li.ui-state-disabled .ui-tabs-anchor,
                        .ui-tabs .ui-tabs-nav li.ui-tabs-active .ui-tabs-anchor,
                        .ui-tabs .ui-tabs-nav li.ui-tabs-loading .ui-tabs-anchor {
                        cursor: text;
                        }
                        .ui-tabs-collapsible .ui-tabs-nav li.ui-tabs-active .ui-tabs-anchor {
                        cursor: pointer;
                        }
                        .ui-tabs .ui-tabs-panel {
                        display: block;
                        border-width: 0;
                        padding: 1em 1.4em;
                        background: 0 0;
                        }
                        .ui-tooltip {
                        padding: 8px;
                        position: absolute;
                        z-index: 9999;
                        max-width: 300px;
                        }
                        body .ui-tooltip {
                        border-width: 2px;
                        }
                        .ui-widget {
                        font-family: Arial, Helvetica, sans-serif;
                        font-size: 1em;
                        }
                        .ui-widget .ui-widget {
                        font-size: 1em;
                        }
                        .ui-widget button,
                        .ui-widget input,
                        .ui-widget select,
                        .ui-widget textarea {
                        font-family: Arial, Helvetica, sans-serif;
                        font-size: 1em;
                        }
                        .ui-widget.ui-widget-content {
                        border: 1px solid #c5c5c5;
                        }
                        .ui-widget-content {
                        border: 1px solid #ddd;
                        background: #fff;
                        color: #333;
                        }
                        .ui-widget-content a {
                        color: #333;
                        }
                        .ui-widget-header {
                        border: 1px solid #ddd;
                        background: #e9e9e9;
                        color: #333;
                        font-weight: 700;
                        }
                        .ui-widget-header a {
                        color: #333;
                        }
                        .ui-button,
                        .ui-state-default,
                        .ui-widget-content .ui-state-default,
                        .ui-widget-header .ui-state-default,
                        html .ui-button.ui-state-disabled:active,
                        html .ui-button.ui-state-disabled:hover {
                        border: 1px solid #c5c5c5;
                        background: #f6f6f6;
                        font-weight: 400;
                        color: #454545;
                        }
                        .ui-button,
                        .ui-state-default a,
                        .ui-state-default a:link,
                        .ui-state-default a:visited,
                        a.ui-button,
                        a:link.ui-button,
                        a:visited.ui-button {
                        color: #454545;
                        text-decoration: none;
                        }
                        .ui-button:focus,
                        .ui-button:hover,
                        .ui-state-focus,
                        .ui-state-hover,
                        .ui-widget-content .ui-state-focus,
                        .ui-widget-content .ui-state-hover,
                        .ui-widget-header .ui-state-focus,
                        .ui-widget-header .ui-state-hover {
                        border: 1px solid #ccc;
                        background: #ededed;
                        font-weight: 400;
                        color: #2b2b2b;
                        }
                        .ui-state-focus a,
                        .ui-state-focus a:hover,
                        .ui-state-focus a:link,
                        .ui-state-focus a:visited,
                        .ui-state-hover a,
                        .ui-state-hover a:hover,
                        .ui-state-hover a:link,
                        .ui-state-hover a:visited,
                        a.ui-button:focus,
                        a.ui-button:hover {
                        color: #2b2b2b;
                        text-decoration: none;
                        }
                        .ui-visual-focus {
                        box-shadow: 0 0 3px 1px #5e9ed6;
                        }
                        .ui-button.ui-state-active:hover,
                        .ui-button:active,
                        .ui-state-active,
                        .ui-widget-content .ui-state-active,
                        .ui-widget-header .ui-state-active,
                        a.ui-button:active {
                        border: 1px solid #003eff;
                        background: #007fff;
                        font-weight: 400;
                        color: #fff;
                        }
                        .ui-icon-background,
                        .ui-state-active .ui-icon-background {
                        border: #003eff;
                        background-color: #fff;
                        }
                        .ui-state-active a,
                        .ui-state-active a:link,
                        .ui-state-active a:visited {
                        color: #fff;
                        text-decoration: none;
                        }
                        .ui-state-highlight,
                        .ui-widget-content .ui-state-highlight,
                        .ui-widget-header .ui-state-highlight {
                        border: 1px solid #dad55e;
                        background: #fffa90;
                        color: #777620;
                        }
                        .ui-state-checked {
                        border: 1px solid #dad55e;
                        background: #fffa90;
                        }
                        .ui-state-highlight a,
                        .ui-widget-content .ui-state-highlight a,
                        .ui-widget-header .ui-state-highlight a {
                        color: #777620;
                        }
                        .ui-state-error,
                        .ui-widget-content .ui-state-error,
                        .ui-widget-header .ui-state-error {
                        border: 1px solid #f1a899;
                        background: #fddfdf;
                        color: #5f3f3f;
                        }
                        .ui-state-error a,
                        .ui-widget-content .ui-state-error a,
                        .ui-widget-header .ui-state-error a {
                        color: #5f3f3f;
                        }
                        .ui-state-error-text,
                        .ui-widget-content .ui-state-error-text,
                        .ui-widget-header .ui-state-error-text {
                        color: #5f3f3f;
                        }
                        .ui-priority-primary,
                        .ui-widget-content .ui-priority-primary,
                        .ui-widget-header .ui-priority-primary {
                        font-weight: 700;
                        }
                        .ui-priority-secondary,
                        .ui-widget-content .ui-priority-secondary,
                        .ui-widget-header .ui-priority-secondary {
                        opacity: 0.7;
                        filter: Alpha(Opacity=70);
                        font-weight: 400;
                        }
                        .ui-state-disabled,
                        .ui-widget-content .ui-state-disabled,
                        .ui-widget-header .ui-state-disabled {
                        opacity: 0.35;
                        filter: Alpha(Opacity=35);
                        background-image: none;
                        }
                        .ui-state-disabled .ui-icon {
                        filter: Alpha(Opacity=35);
                        }
                        .ui-icon {
                        width: 16px;
                        height: 16px;
                        }
                        .ui-icon,
                        .ui-widget-content .ui-icon {
                        background-image: url(images/ui-icons_444444_256x240.png);
                        }
                        .ui-widget-header .ui-icon {
                        background-image: url(images/ui-icons_444444_256x240.png);
                        }
                        .ui-button:focus .ui-icon,
                        .ui-button:hover .ui-icon,
                        .ui-state-focus .ui-icon,
                        .ui-state-hover .ui-icon {
                        background-image: url(images/ui-icons_555555_256x240.png);
                        }
                        .ui-button:active .ui-icon,
                        .ui-state-active .ui-icon {
                        background-image: url(images/ui-icons_ffffff_256x240.png);
                        }
                        .ui-button .ui-state-highlight.ui-icon,
                        .ui-state-highlight .ui-icon {
                        background-image: url(images/ui-icons_777620_256x240.png);
                        }
                        .ui-state-error .ui-icon,
                        .ui-state-error-text .ui-icon {
                        background-image: url(images/ui-icons_cc0000_256x240.png);
                        }
                        .ui-button .ui-icon {
                        background-image: url(images/ui-icons_777777_256x240.png);
                        }
                        .ui-icon-blank {
                        background-position: 16px 16px;
                        }
                        .ui-icon-caret-1-n {
                        background-position: 0 0;
                        }
                        .ui-icon-caret-1-ne {
                        background-position: -16px 0;
                        }
                        .ui-icon-caret-1-e {
                        background-position: -32px 0;
                        }
                        .ui-icon-caret-1-se {
                        background-position: -48px 0;
                        }
                        .ui-icon-caret-1-s {
                        background-position: -65px 0;
                        }
                        .ui-icon-caret-1-sw {
                        background-position: -80px 0;
                        }
                        .ui-icon-caret-1-w {
                        background-position: -96px 0;
                        }
                        .ui-icon-caret-1-nw {
                        background-position: -112px 0;
                        }
                        .ui-icon-caret-2-n-s {
                        background-position: -128px 0;
                        }
                        .ui-icon-caret-2-e-w {
                        background-position: -144px 0;
                        }
                        .ui-icon-triangle-1-n {
                        background-position: 0 -16px;
                        }
                        .ui-icon-triangle-1-ne {
                        background-position: -16px -16px;
                        }
                        .ui-icon-triangle-1-e {
                        background-position: -32px -16px;
                        }
                        .ui-icon-triangle-1-se {
                        background-position: -48px -16px;
                        }
                        .ui-icon-triangle-1-s {
                        background-position: -65px -16px;
                        }
                        .ui-icon-triangle-1-sw {
                        background-position: -80px -16px;
                        }
                        .ui-icon-triangle-1-w {
                        background-position: -96px -16px;
                        }
                        .ui-icon-triangle-1-nw {
                        background-position: -112px -16px;
                        }
                        .ui-icon-triangle-2-n-s {
                        background-position: -128px -16px;
                        }
                        .ui-icon-triangle-2-e-w {
                        background-position: -144px -16px;
                        }
                        .ui-icon-arrow-1-n {
                        background-position: 0 -32px;
                        }
                        .ui-icon-arrow-1-ne {
                        background-position: -16px -32px;
                        }
                        .ui-icon-arrow-1-e {
                        background-position: -32px -32px;
                        }
                        .ui-icon-arrow-1-se {
                        background-position: -48px -32px;
                        }
                        .ui-icon-arrow-1-s {
                        background-position: -65px -32px;
                        }
                        .ui-icon-arrow-1-sw {
                        background-position: -80px -32px;
                        }
                        .ui-icon-arrow-1-w {
                        background-position: -96px -32px;
                        }
                        .ui-icon-arrow-1-nw {
                        background-position: -112px -32px;
                        }
                        .ui-icon-arrow-2-n-s {
                        background-position: -128px -32px;
                        }
                        .ui-icon-arrow-2-ne-sw {
                        background-position: -144px -32px;
                        }
                        .ui-icon-arrow-2-e-w {
                        background-position: -160px -32px;
                        }
                        .ui-icon-arrow-2-se-nw {
                        background-position: -176px -32px;
                        }
                        .ui-icon-arrowstop-1-n {
                        background-position: -192px -32px;
                        }
                        .ui-icon-arrowstop-1-e {
                        background-position: -208px -32px;
                        }
                        .ui-icon-arrowstop-1-s {
                        background-position: -224px -32px;
                        }
                        .ui-icon-arrowstop-1-w {
                        background-position: -240px -32px;
                        }
                        .ui-icon-arrowthick-1-n {
                        background-position: 1px -48px;
                        }
                        .ui-icon-arrowthick-1-ne {
                        background-position: -16px -48px;
                        }
                        .ui-icon-arrowthick-1-e {
                        background-position: -32px -48px;
                        }
                        .ui-icon-arrowthick-1-se {
                        background-position: -48px -48px;
                        }
                        .ui-icon-arrowthick-1-s {
                        background-position: -64px -48px;
                        }
                        .ui-icon-arrowthick-1-sw {
                        background-position: -80px -48px;
                        }
                        .ui-icon-arrowthick-1-w {
                        background-position: -96px -48px;
                        }
                        .ui-icon-arrowthick-1-nw {
                        background-position: -112px -48px;
                        }
                        .ui-icon-arrowthick-2-n-s {
                        background-position: -128px -48px;
                        }
                        .ui-icon-arrowthick-2-ne-sw {
                        background-position: -144px -48px;
                        }
                        .ui-icon-arrowthick-2-e-w {
                        background-position: -160px -48px;
                        }
                        .ui-icon-arrowthick-2-se-nw {
                        background-position: -176px -48px;
                        }
                        .ui-icon-arrowthickstop-1-n {
                        background-position: -192px -48px;
                        }
                        .ui-icon-arrowthickstop-1-e {
                        background-position: -208px -48px;
                        }
                        .ui-icon-arrowthickstop-1-s {
                        background-position: -224px -48px;
                        }
                        .ui-icon-arrowthickstop-1-w {
                        background-position: -240px -48px;
                        }
                        .ui-icon-arrowreturnthick-1-w {
                        background-position: 0 -64px;
                        }
                        .ui-icon-arrowreturnthick-1-n {
                        background-position: -16px -64px;
                        }
                        .ui-icon-arrowreturnthick-1-e {
                        background-position: -32px -64px;
                        }
                        .ui-icon-arrowreturnthick-1-s {
                        background-position: -48px -64px;
                        }
                        .ui-icon-arrowreturn-1-w {
                        background-position: -64px -64px;
                        }
                        .ui-icon-arrowreturn-1-n {
                        background-position: -80px -64px;
                        }
                        .ui-icon-arrowreturn-1-e {
                        background-position: -96px -64px;
                        }
                        .ui-icon-arrowreturn-1-s {
                        background-position: -112px -64px;
                        }
                        .ui-icon-arrowrefresh-1-w {
                        background-position: -128px -64px;
                        }
                        .ui-icon-arrowrefresh-1-n {
                        background-position: -144px -64px;
                        }
                        .ui-icon-arrowrefresh-1-e {
                        background-position: -160px -64px;
                        }
                        .ui-icon-arrowrefresh-1-s {
                        background-position: -176px -64px;
                        }
                        .ui-icon-arrow-4 {
                        background-position: 0 -80px;
                        }
                        .ui-icon-arrow-4-diag {
                        background-position: -16px -80px;
                        }
                        .ui-icon-extlink {
                        background-position: -32px -80px;
                        }
                        .ui-icon-newwin {
                        background-position: -48px -80px;
                        }
                        .ui-icon-refresh {
                        background-position: -64px -80px;
                        }
                        .ui-icon-shuffle {
                        background-position: -80px -80px;
                        }
                        .ui-icon-transfer-e-w {
                        background-position: -96px -80px;
                        }
                        .ui-icon-transferthick-e-w {
                        background-position: -112px -80px;
                        }
                        .ui-icon-folder-collapsed {
                        background-position: 0 -96px;
                        }
                        .ui-icon-folder-open {
                        background-position: -16px -96px;
                        }
                        .ui-icon-document {
                        background-position: -32px -96px;
                        }
                        .ui-icon-document-b {
                        background-position: -48px -96px;
                        }
                        .ui-icon-note {
                        background-position: -64px -96px;
                        }
                        .ui-icon-mail-closed {
                        background-position: -80px -96px;
                        }
                        .ui-icon-mail-open {
                        background-position: -96px -96px;
                        }
                        .ui-icon-suitcase {
                        background-position: -112px -96px;
                        }
                        .ui-icon-comment {
                        background-position: -128px -96px;
                        }
                        .ui-icon-person {
                        background-position: -144px -96px;
                        }
                        .ui-icon-print {
                        background-position: -160px -96px;
                        }
                        .ui-icon-trash {
                        background-position: -176px -96px;
                        }
                        .ui-icon-locked {
                        background-position: -192px -96px;
                        }
                        .ui-icon-unlocked {
                        background-position: -208px -96px;
                        }
                        .ui-icon-bookmark {
                        background-position: -224px -96px;
                        }
                        .ui-icon-tag {
                        background-position: -240px -96px;
                        }
                        .ui-icon-home {
                        background-position: 0 -112px;
                        }
                        .ui-icon-flag {
                        background-position: -16px -112px;
                        }
                        .ui-icon-calendar {
                        background-position: -32px -112px;
                        }
                        .ui-icon-cart {
                        background-position: -48px -112px;
                        }
                        .ui-icon-pencil {
                        background-position: -64px -112px;
                        }
                        .ui-icon-clock {
                        background-position: -80px -112px;
                        }
                        .ui-icon-disk {
                        background-position: -96px -112px;
                        }
                        .ui-icon-calculator {
                        background-position: -112px -112px;
                        }
                        .ui-icon-zoomin {
                        background-position: -128px -112px;
                        }
                        .ui-icon-zoomout {
                        background-position: -144px -112px;
                        }
                        .ui-icon-search {
                        background-position: -160px -112px;
                        }
                        .ui-icon-wrench {
                        background-position: -176px -112px;
                        }
                        .ui-icon-gear {
                        background-position: -192px -112px;
                        }
                        .ui-icon-heart {
                        background-position: -208px -112px;
                        }
                        .ui-icon-star {
                        background-position: -224px -112px;
                        }
                        .ui-icon-link {
                        background-position: -240px -112px;
                        }
                        .ui-icon-cancel {
                        background-position: 0 -128px;
                        }
                        .ui-icon-plus {
                        background-position: -16px -128px;
                        }
                        .ui-icon-plusthick {
                        background-position: -32px -128px;
                        }
                        .ui-icon-minus {
                        background-position: -48px -128px;
                        }
                        .ui-icon-minusthick {
                        background-position: -64px -128px;
                        }
                        .ui-icon-close {
                        background-position: -80px -128px;
                        }
                        .ui-icon-closethick {
                        background-position: -96px -128px;
                        }
                        .ui-icon-key {
                        background-position: -112px -128px;
                        }
                        .ui-icon-lightbulb {
                        background-position: -128px -128px;
                        }
                        .ui-icon-scissors {
                        background-position: -144px -128px;
                        }
                        .ui-icon-clipboard {
                        background-position: -160px -128px;
                        }
                        .ui-icon-copy {
                        background-position: -176px -128px;
                        }
                        .ui-icon-contact {
                        background-position: -192px -128px;
                        }
                        .ui-icon-image {
                        background-position: -208px -128px;
                        }
                        .ui-icon-video {
                        background-position: -224px -128px;
                        }
                        .ui-icon-script {
                        background-position: -240px -128px;
                        }
                        .ui-icon-alert {
                        background-position: 0 -144px;
                        }
                        .ui-icon-info {
                        background-position: -16px -144px;
                        }
                        .ui-icon-notice {
                        background-position: -32px -144px;
                        }
                        .ui-icon-help {
                        background-position: -48px -144px;
                        }
                        .ui-icon-check {
                        background-position: -64px -144px;
                        }
                        .ui-icon-bullet {
                        background-position: -80px -144px;
                        }
                        .ui-icon-radio-on {
                        background-position: -96px -144px;
                        }
                        .ui-icon-radio-off {
                        background-position: -112px -144px;
                        }
                        .ui-icon-pin-w {
                        background-position: -128px -144px;
                        }
                        .ui-icon-pin-s {
                        background-position: -144px -144px;
                        }
                        .ui-icon-play {
                        background-position: 0 -160px;
                        }
                        .ui-icon-pause {
                        background-position: -16px -160px;
                        }
                        .ui-icon-seek-next {
                        background-position: -32px -160px;
                        }
                        .ui-icon-seek-prev {
                        background-position: -48px -160px;
                        }
                        .ui-icon-seek-end {
                        background-position: -64px -160px;
                        }
                        .ui-icon-seek-start {
                        background-position: -80px -160px;
                        }
                        .ui-icon-seek-first {
                        background-position: -80px -160px;
                        }
                        .ui-icon-stop {
                        background-position: -96px -160px;
                        }
                        .ui-icon-eject {
                        background-position: -112px -160px;
                        }
                        .ui-icon-volume-off {
                        background-position: -128px -160px;
                        }
                        .ui-icon-volume-on {
                        background-position: -144px -160px;
                        }
                        .ui-icon-power {
                        background-position: 0 -176px;
                        }
                        .ui-icon-signal-diag {
                        background-position: -16px -176px;
                        }
                        .ui-icon-signal {
                        background-position: -32px -176px;
                        }
                        .ui-icon-battery-0 {
                        background-position: -48px -176px;
                        }
                        .ui-icon-battery-1 {
                        background-position: -64px -176px;
                        }
                        .ui-icon-battery-2 {
                        background-position: -80px -176px;
                        }
                        .ui-icon-battery-3 {
                        background-position: -96px -176px;
                        }
                        .ui-icon-circle-plus {
                        background-position: 0 -192px;
                        }
                        .ui-icon-circle-minus {
                        background-position: -16px -192px;
                        }
                        .ui-icon-circle-close {
                        background-position: -32px -192px;
                        }
                        .ui-icon-circle-triangle-e {
                        background-position: -48px -192px;
                        }
                        .ui-icon-circle-triangle-s {
                        background-position: -64px -192px;
                        }
                        .ui-icon-circle-triangle-w {
                        background-position: -80px -192px;
                        }
                        .ui-icon-circle-triangle-n {
                        background-position: -96px -192px;
                        }
                        .ui-icon-circle-arrow-e {
                        background-position: -112px -192px;
                        }
                        .ui-icon-circle-arrow-s {
                        background-position: -128px -192px;
                        }
                        .ui-icon-circle-arrow-w {
                        background-position: -144px -192px;
                        }
                        .ui-icon-circle-arrow-n {
                        background-position: -160px -192px;
                        }
                        .ui-icon-circle-zoomin {
                        background-position: -176px -192px;
                        }
                        .ui-icon-circle-zoomout {
                        background-position: -192px -192px;
                        }
                        .ui-icon-circle-check {
                        background-position: -208px -192px;
                        }
                        .ui-icon-circlesmall-plus {
                        background-position: 0 -208px;
                        }
                        .ui-icon-circlesmall-minus {
                        background-position: -16px -208px;
                        }
                        .ui-icon-circlesmall-close {
                        background-position: -32px -208px;
                        }
                        .ui-icon-squaresmall-plus {
                        background-position: -48px -208px;
                        }
                        .ui-icon-squaresmall-minus {
                        background-position: -64px -208px;
                        }
                        .ui-icon-squaresmall-close {
                        background-position: -80px -208px;
                        }
                        .ui-icon-grip-dotted-vertical {
                        background-position: 0 -224px;
                        }
                        .ui-icon-grip-dotted-horizontal {
                        background-position: -16px -224px;
                        }
                        .ui-icon-grip-solid-vertical {
                        background-position: -32px -224px;
                        }
                        .ui-icon-grip-solid-horizontal {
                        background-position: -48px -224px;
                        }
                        .ui-icon-gripsmall-diagonal-se {
                        background-position: -64px -224px;
                        }
                        .ui-icon-grip-diagonal-se {
                        background-position: -80px -224px;
                        }
                        .ui-corner-all,
                        .ui-corner-left,
                        .ui-corner-tl,
                        .ui-corner-top {
                        border-top-left-radius: 3px;
                        }
                        .ui-corner-all,
                        .ui-corner-right,
                        .ui-corner-top,
                        .ui-corner-tr {
                        border-top-right-radius: 3px;
                        }
                        .ui-corner-all,
                        .ui-corner-bl,
                        .ui-corner-bottom,
                        .ui-corner-left {
                        border-bottom-left-radius: 3px;
                        }
                        .ui-corner-all,
                        .ui-corner-bottom,
                        .ui-corner-br,
                        .ui-corner-right {
                        border-bottom-right-radius: 3px;
                        }
                        .ui-widget-overlay {
                        background: #aaa;
                        opacity: 0.003;
                        filter: Alpha(Opacity=.3);
                        }
                        .ui-widget-shadow {
                        -webkit-box-shadow: 0 0 5px #666;
                        box-shadow: 0 0 5px #666;
                        } /*!
                        *  Font Awesome 4.7.0 by @davegandy - http://fontawesome.io - @fontawesome
                        *  License - http://fontawesome.io/license (Font: SIL OFL 1.1, CSS: MIT License)
                        */
                        @font-face {
                        font-family: FontAwesome;
                        src: url(../fonts/fontawesome-webfont.eot?v=4.7.0);
                        src: url(../fonts/fontawesome-webfont.eot?#iefix&v=4.7.0)
                            format("embedded-opentype"),
                            url(../fonts/fontawesome-webfont.woff2?v=4.7.0) format("woff2"),
                            url(../fonts/fontawesome-webfont.woff?v=4.7.0) format("woff"),
                            url(../fonts/fontawesome-webfont.ttf?v=4.7.0) format("truetype"),
                            url(../fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular)
                            format("svg");
                        font-weight: 400;
                        font-style: normal;
                        }
                        .fa {
                        display: inline-block;
                        font: normal normal normal 14px/1 FontAwesome;
                        font-size: inherit;
                        text-rendering: auto;
                        -webkit-font-smoothing: antialiased;
                        -moz-osx-font-smoothing: grayscale;
                        }
                        .fa-lg {
                        font-size: 1.33333333em;
                        line-height: 0.75em;
                        vertical-align: -15%;
                        }
                        .fa-2x {
                        font-size: 2em;
                        }
                        .fa-3x {
                        font-size: 3em;
                        }
                        .fa-4x {
                        font-size: 4em;
                        }
                        .fa-5x {
                        font-size: 5em;
                        }
                        .fa-fw {
                        width: 1.28571429em;
                        text-align: center;
                        }
                        .fa-ul {
                        padding-left: 0;
                        margin-left: 2.14285714em;
                        list-style-type: none;
                        }
                        .fa-ul > li {
                        position: relative;
                        }
                        .fa-li {
                        position: absolute;
                        left: -2.14285714em;
                        width: 2.14285714em;
                        top: 0.14285714em;
                        text-align: center;
                        }
                        .fa-li.fa-lg {
                        left: -1.85714286em;
                        }
                        .fa-border {
                        padding: 0.2em 0.25em 0.15em;
                        border: solid 0.08em #eee;
                        border-radius: 0.1em;
                        }
                        .fa-pull-left {
                        float: left;
                        }
                        .fa-pull-right {
                        float: right;
                        }
                        .fa.fa-pull-left {
                        margin-right: 0.3em;
                        }
                        .fa.fa-pull-right {
                        margin-left: 0.3em;
                        }
                        .pull-right {
                        float: right;
                        }
                        .pull-left {
                        float: left;
                        }
                        .fa.pull-left {
                        margin-right: 0.3em;
                        }
                        .fa.pull-right {
                        margin-left: 0.3em;
                        }
                        .fa-spin {
                        -webkit-animation: fa-spin 2s infinite linear;
                        animation: fa-spin 2s infinite linear;
                        }
                        .fa-pulse {
                        -webkit-animation: fa-spin 1s infinite steps(8);
                        animation: fa-spin 1s infinite steps(8);
                        }
                        @-webkit-keyframes fa-spin {
                        0% {
                            -webkit-transform: rotate(0);
                            transform: rotate(0);
                        }
                        100% {
                            -webkit-transform: rotate(359deg);
                            transform: rotate(359deg);
                        }
                        }
                        @keyframes fa-spin {
                        0% {
                            -webkit-transform: rotate(0);
                            transform: rotate(0);
                        }
                        100% {
                            -webkit-transform: rotate(359deg);
                            transform: rotate(359deg);
                        }
                        }
                        .fa-rotate-90 {
                        -webkit-transform: rotate(90deg);
                        -ms-transform: rotate(90deg);
                        transform: rotate(90deg);
                        }
                        .fa-rotate-180 {
                        -webkit-transform: rotate(180deg);
                        -ms-transform: rotate(180deg);
                        transform: rotate(180deg);
                        }
                        .fa-rotate-270 {
                        -webkit-transform: rotate(270deg);
                        -ms-transform: rotate(270deg);
                        transform: rotate(270deg);
                        }
                        .fa-flip-horizontal {
                        -webkit-transform: scale(-1, 1);
                        -ms-transform: scale(-1, 1);
                        transform: scale(-1, 1);
                        }
                        .fa-flip-vertical {
                        -webkit-transform: scale(1, -1);
                        -ms-transform: scale(1, -1);
                        transform: scale(1, -1);
                        }
                        :root .fa-flip-horizontal,
                        :root .fa-flip-vertical,
                        :root .fa-rotate-180,
                        :root .fa-rotate-270,
                        :root .fa-rotate-90 {
                        filter: none;
                        }
                        .fa-stack {
                        position: relative;
                        display: inline-block;
                        width: 2em;
                        height: 2em;
                        line-height: 2em;
                        vertical-align: middle;
                        }
                        .fa-stack-1x,
                        .fa-stack-2x {
                        position: absolute;
                        left: 0;
                        width: 100%;
                        text-align: center;
                        }
                        .fa-stack-1x {
                        line-height: inherit;
                        }
                        .fa-stack-2x {
                        font-size: 2em;
                        }
                        .fa-inverse {
                        color: #fff;
                        }
                        .fa-glass:before {
                        content: "\f000";
                        }
                        .fa-music:before {
                        content: "\f001";
                        }
                        .fa-search:before {
                        content: "\f002";
                        }
                        .fa-envelope-o:before {
                        content: "\f003";
                        }
                        .fa-heart:before {
                        content: "\f004";
                        }
                        .fa-star:before {
                        content: "\f005";
                        }
                        .fa-star-o:before {
                        content: "\f006";
                        }
                        .fa-user:before {
                        content: "\f007";
                        }
                        .fa-film:before {
                        content: "\f008";
                        }
                        .fa-th-large:before {
                        content: "\f009";
                        }
                        .fa-th:before {
                        content: "\f00a";
                        }
                        .fa-th-list:before {
                        content: "\f00b";
                        }
                        .fa-check:before {
                        content: "\f00c";
                        }
                        .fa-close:before,
                        .fa-remove:before,
                        .fa-times:before {
                        content: "\f00d";
                        }
                        .fa-search-plus:before {
                        content: "\f00e";
                        }
                        .fa-search-minus:before {
                        content: "\f010";
                        }
                        .fa-power-off:before {
                        content: "\f011";
                        }
                        .fa-signal:before {
                        content: "\f012";
                        }
                        .fa-cog:before,
                        .fa-gear:before {
                        content: "\f013";
                        }
                        .fa-trash-o:before {
                        content: "\f014";
                        }
                        .fa-home:before {
                        content: "\f015";
                        }
                        .fa-file-o:before {
                        content: "\f016";
                        }
                        .fa-clock-o:before {
                        content: "\f017";
                        }
                        .fa-road:before {
                        content: "\f018";
                        }
                        .fa-download:before {
                        content: "\f019";
                        }
                        .fa-arrow-circle-o-down:before {
                        content: "\f01a";
                        }
                        .fa-arrow-circle-o-up:before {
                        content: "\f01b";
                        }
                        .fa-inbox:before {
                        content: "\f01c";
                        }
                        .fa-play-circle-o:before {
                        content: "\f01d";
                        }
                        .fa-repeat:before,
                        .fa-rotate-right:before {
                        content: "\f01e";
                        }
                        .fa-refresh:before {
                        content: "\f021";
                        }
                        .fa-list-alt:before {
                        content: "\f022";
                        }
                        .fa-lock:before {
                        content: "\f023";
                        }
                        .fa-flag:before {
                        content: "\f024";
                        }
                        .fa-headphones:before {
                        content: "\f025";
                        }
                        .fa-volume-off:before {
                        content: "\f026";
                        }
                        .fa-volume-down:before {
                        content: "\f027";
                        }
                        .fa-volume-up:before {
                        content: "\f028";
                        }
                        .fa-qrcode:before {
                        content: "\f029";
                        }
                        .fa-barcode:before {
                        content: "\f02a";
                        }
                        .fa-tag:before {
                        content: "\f02b";
                        }
                        .fa-tags:before {
                        content: "\f02c";
                        }
                        .fa-book:before {
                        content: "\f02d";
                        }
                        .fa-bookmark:before {
                        content: "\f02e";
                        }
                        .fa-print:before {
                        content: "\f02f";
                        }
                        .fa-camera:before {
                        content: "\f030";
                        }
                        .fa-font:before {
                        content: "\f031";
                        }
                        .fa-bold:before {
                        content: "\f032";
                        }
                        .fa-italic:before {
                        content: "\f033";
                        }
                        .fa-text-height:before {
                        content: "\f034";
                        }
                        .fa-text-width:before {
                        content: "\f035";
                        }
                        .fa-align-left:before {
                        content: "\f036";
                        }
                        .fa-align-center:before {
                        content: "\f037";
                        }
                        .fa-align-right:before {
                        content: "\f038";
                        }
                        .fa-align-justify:before {
                        content: "\f039";
                        }
                        .fa-list:before {
                        content: "\f03a";
                        }
                        .fa-dedent:before,
                        .fa-outdent:before {
                        content: "\f03b";
                        }
                        .fa-indent:before {
                        content: "\f03c";
                        }
                        .fa-video-camera:before {
                        content: "\f03d";
                        }
                        .fa-image:before,
                        .fa-photo:before,
                        .fa-picture-o:before {
                        content: "\f03e";
                        }
                        .fa-pencil:before {
                        content: "\f040";
                        }
                        .fa-map-marker:before {
                        content: "\f041";
                        }
                        .fa-adjust:before {
                        content: "\f042";
                        }
                        .fa-tint:before {
                        content: "\f043";
                        }
                        .fa-edit:before,
                        .fa-pencil-square-o:before {
                        content: "\f044";
                        }
                        .fa-share-square-o:before {
                        content: "\f045";
                        }
                        .fa-check-square-o:before {
                        content: "\f046";
                        }
                        .fa-arrows:before {
                        content: "\f047";
                        }
                        .fa-step-backward:before {
                        content: "\f048";
                        }
                        .fa-fast-backward:before {
                        content: "\f049";
                        }
                        .fa-backward:before {
                        content: "\f04a";
                        }
                        .fa-play:before {
                        content: "\f04b";
                        }
                        .fa-pause:before {
                        content: "\f04c";
                        }
                        .fa-stop:before {
                        content: "\f04d";
                        }
                        .fa-forward:before {
                        content: "\f04e";
                        }
                        .fa-fast-forward:before {
                        content: "\f050";
                        }
                        .fa-step-forward:before {
                        content: "\f051";
                        }
                        .fa-eject:before {
                        content: "\f052";
                        }
                        .fa-chevron-left:before {
                        content: "\f053";
                        }
                        .fa-chevron-right:before {
                        content: "\f054";
                        }
                        .fa-plus-circle:before {
                        content: "\f055";
                        }
                        .fa-minus-circle:before {
                        content: "\f056";
                        }
                        .fa-times-circle:before {
                        content: "\f057";
                        }
                        .fa-check-circle:before {
                        content: "\f058";
                        }
                        .fa-question-circle:before {
                        content: "\f059";
                        }
                        .fa-info-circle:before {
                        content: "\f05a";
                        }
                        .fa-crosshairs:before {
                        content: "\f05b";
                        }
                        .fa-times-circle-o:before {
                        content: "\f05c";
                        }
                        .fa-check-circle-o:before {
                        content: "\f05d";
                        }
                        .fa-ban:before {
                        content: "\f05e";
                        }
                        .fa-arrow-left:before {
                        content: "\f060";
                        }
                        .fa-arrow-right:before {
                        content: "\f061";
                        }
                        .fa-arrow-up:before {
                        content: "\f062";
                        }
                        .fa-arrow-down:before {
                        content: "\f063";
                        }
                        .fa-mail-forward:before,
                        .fa-share:before {
                        content: "\f064";
                        }
                        .fa-expand:before {
                        content: "\f065";
                        }
                        .fa-compress:before {
                        content: "\f066";
                        }
                        .fa-plus:before {
                        content: "\f067";
                        }
                        .fa-minus:before {
                        content: "\f068";
                        }
                        .fa-asterisk:before {
                        content: "\f069";
                        }
                        .fa-exclamation-circle:before {
                        content: "\f06a";
                        }
                        .fa-gift:before {
                        content: "\f06b";
                        }
                        .fa-leaf:before {
                        content: "\f06c";
                        }
                        .fa-fire:before {
                        content: "\f06d";
                        }
                        .fa-eye:before {
                        content: "\f06e";
                        }
                        .fa-eye-slash:before {
                        content: "\f070";
                        }
                        .fa-exclamation-triangle:before,
                        .fa-warning:before {
                        content: "\f071";
                        }
                        .fa-plane:before {
                        content: "\f072";
                        }
                        .fa-calendar:before {
                        content: "\f073";
                        }
                        .fa-random:before {
                        content: "\f074";
                        }
                        .fa-comment:before {
                        content: "\f075";
                        }
                        .fa-magnet:before {
                        content: "\f076";
                        }
                        .fa-chevron-up:before {
                        content: "\f077";
                        }
                        .fa-chevron-down:before {
                        content: "\f078";
                        }
                        .fa-retweet:before {
                        content: "\f079";
                        }
                        .fa-shopping-cart:before {
                        content: "\f07a";
                        }
                        .fa-folder:before {
                        content: "\f07b";
                        }
                        .fa-folder-open:before {
                        content: "\f07c";
                        }
                        .fa-arrows-v:before {
                        content: "\f07d";
                        }
                        .fa-arrows-h:before {
                        content: "\f07e";
                        }
                        .fa-bar-chart-o:before,
                        .fa-bar-chart:before {
                        content: "\f080";
                        }
                        .fa-twitter-square:before {
                        content: "\f081";
                        }
                        .fa-facebook-square:before {
                        content: "\f082";
                        }
                        .fa-camera-retro:before {
                        content: "\f083";
                        }
                        .fa-key:before {
                        content: "\f084";
                        }
                        .fa-cogs:before,
                        .fa-gears:before {
                        content: "\f085";
                        }
                        .fa-comments:before {
                        content: "\f086";
                        }
                        .fa-thumbs-o-up:before {
                        content: "\f087";
                        }
                        .fa-thumbs-o-down:before {
                        content: "\f088";
                        }
                        .fa-star-half:before {
                        content: "\f089";
                        }
                        .fa-heart-o:before {
                        content: "\f08a";
                        }
                        .fa-sign-out:before {
                        content: "\f08b";
                        }
                        .fa-linkedin-square:before {
                        content: "\f08c";
                        }
                        .fa-thumb-tack:before {
                        content: "\f08d";
                        }
                        .fa-external-link:before {
                        content: "\f08e";
                        }
                        .fa-sign-in:before {
                        content: "\f090";
                        }
                        .fa-trophy:before {
                        content: "\f091";
                        }
                        .fa-github-square:before {
                        content: "\f092";
                        }
                        .fa-upload:before {
                        content: "\f093";
                        }
                        .fa-lemon-o:before {
                        content: "\f094";
                        }
                        .fa-phone:before {
                        content: "\f095";
                        }
                        .fa-square-o:before {
                        content: "\f096";
                        }
                        .fa-bookmark-o:before {
                        content: "\f097";
                        }
                        .fa-phone-square:before {
                        content: "\f098";
                        }
                        .fa-twitter:before {
                        content: "\f099";
                        }
                        .fa-facebook-f:before,
                        .fa-facebook:before {
                        content: "\f09a";
                        }
                        .fa-github:before {
                        content: "\f09b";
                        }
                        .fa-unlock:before {
                        content: "\f09c";
                        }
                        .fa-credit-card:before {
                        content: "\f09d";
                        }
                        .fa-feed:before,
                        .fa-rss:before {
                        content: "\f09e";
                        }
                        .fa-hdd-o:before {
                        content: "\f0a0";
                        }
                        .fa-bullhorn:before {
                        content: "\f0a1";
                        }
                        .fa-bell:before {
                        content: "\f0f3";
                        }
                        .fa-certificate:before {
                        content: "\f0a3";
                        }
                        .fa-hand-o-right:before {
                        content: "\f0a4";
                        }
                        .fa-hand-o-left:before {
                        content: "\f0a5";
                        }
                        .fa-hand-o-up:before {
                        content: "\f0a6";
                        }
                        .fa-hand-o-down:before {
                        content: "\f0a7";
                        }
                        .fa-arrow-circle-left:before {
                        content: "\f0a8";
                        }
                        .fa-arrow-circle-right:before {
                        content: "\f0a9";
                        }
                        .fa-arrow-circle-up:before {
                        content: "\f0aa";
                        }
                        .fa-arrow-circle-down:before {
                        content: "\f0ab";
                        }
                        .fa-globe:before {
                        content: "\f0ac";
                        }
                        .fa-wrench:before {
                        content: "\f0ad";
                        }
                        .fa-tasks:before {
                        content: "\f0ae";
                        }
                        .fa-filter:before {
                        content: "\f0b0";
                        }
                        .fa-briefcase:before {
                        content: "\f0b1";
                        }
                        .fa-arrows-alt:before {
                        content: "\f0b2";
                        }
                        .fa-group:before,
                        .fa-users:before {
                        content: "\f0c0";
                        }
                        .fa-chain:before,
                        .fa-link:before {
                        content: "\f0c1";
                        }
                        .fa-cloud:before {
                        content: "\f0c2";
                        }
                        .fa-flask:before {
                        content: "\f0c3";
                        }
                        .fa-cut:before,
                        .fa-scissors:before {
                        content: "\f0c4";
                        }
                        .fa-copy:before,
                        .fa-files-o:before {
                        content: "\f0c5";
                        }
                        .fa-paperclip:before {
                        content: "\f0c6";
                        }
                        .fa-floppy-o:before,
                        .fa-save:before {
                        content: "\f0c7";
                        }
                        .fa-square:before {
                        content: "\f0c8";
                        }
                        .fa-bars:before,
                        .fa-navicon:before,
                        .fa-reorder:before {
                        content: "\f0c9";
                        }
                        .fa-list-ul:before {
                        content: "\f0ca";
                        }
                        .fa-list-ol:before {
                        content: "\f0cb";
                        }
                        .fa-strikethrough:before {
                        content: "\f0cc";
                        }
                        .fa-underline:before {
                        content: "\f0cd";
                        }
                        .fa-table:before {
                        content: "\f0ce";
                        }
                        .fa-magic:before {
                        content: "\f0d0";
                        }
                        .fa-truck:before {
                        content: "\f0d1";
                        }
                        .fa-pinterest:before {
                        content: "\f0d2";
                        }
                        .fa-pinterest-square:before {
                        content: "\f0d3";
                        }
                        .fa-google-plus-square:before {
                        content: "\f0d4";
                        }
                        .fa-google-plus:before {
                        content: "\f0d5";
                        }
                        .fa-money:before {
                        content: "\f0d6";
                        }
                        .fa-caret-down:before {
                        content: "\f0d7";
                        }
                        .fa-caret-up:before {
                        content: "\f0d8";
                        }
                        .fa-caret-left:before {
                        content: "\f0d9";
                        }
                        .fa-caret-right:before {
                        content: "\f0da";
                        }
                        .fa-columns:before {
                        content: "\f0db";
                        }
                        .fa-sort:before,
                        .fa-unsorted:before {
                        content: "\f0dc";
                        }
                        .fa-sort-desc:before,
                        .fa-sort-down:before {
                        content: "\f0dd";
                        }
                        .fa-sort-asc:before,
                        .fa-sort-up:before {
                        content: "\f0de";
                        }
                        .fa-envelope:before {
                        content: "\f0e0";
                        }
                        .fa-linkedin:before {
                        content: "\f0e1";
                        }
                        .fa-rotate-left:before,
                        .fa-undo:before {
                        content: "\f0e2";
                        }
                        .fa-gavel:before,
                        .fa-legal:before {
                        content: "\f0e3";
                        }
                        .fa-dashboard:before,
                        .fa-tachometer:before {
                        content: "\f0e4";
                        }
                        .fa-comment-o:before {
                        content: "\f0e5";
                        }
                        .fa-comments-o:before {
                        content: "\f0e6";
                        }
                        .fa-bolt:before,
                        .fa-flash:before {
                        content: "\f0e7";
                        }
                        .fa-sitemap:before {
                        content: "\f0e8";
                        }
                        .fa-umbrella:before {
                        content: "\f0e9";
                        }
                        .fa-clipboard:before,
                        .fa-paste:before {
                        content: "\f0ea";
                        }
                        .fa-lightbulb-o:before {
                        content: "\f0eb";
                        }
                        .fa-exchange:before {
                        content: "\f0ec";
                        }
                        .fa-cloud-download:before {
                        content: "\f0ed";
                        }
                        .fa-cloud-upload:before {
                        content: "\f0ee";
                        }
                        .fa-user-md:before {
                        content: "\f0f0";
                        }
                        .fa-stethoscope:before {
                        content: "\f0f1";
                        }
                        .fa-suitcase:before {
                        content: "\f0f2";
                        }
                        .fa-bell-o:before {
                        content: "\f0a2";
                        }
                        .fa-coffee:before {
                        content: "\f0f4";
                        }
                        .fa-cutlery:before {
                        content: "\f0f5";
                        }
                        .fa-file-text-o:before {
                        content: "\f0f6";
                        }
                        .fa-building-o:before {
                        content: "\f0f7";
                        }
                        .fa-hospital-o:before {
                        content: "\f0f8";
                        }
                        .fa-ambulance:before {
                        content: "\f0f9";
                        }
                        .fa-medkit:before {
                        content: "\f0fa";
                        }
                        .fa-fighter-jet:before {
                        content: "\f0fb";
                        }
                        .fa-beer:before {
                        content: "\f0fc";
                        }
                        .fa-h-square:before {
                        content: "\f0fd";
                        }
                        .fa-plus-square:before {
                        content: "\f0fe";
                        }
                        .fa-angle-double-left:before {
                        content: "\f100";
                        }
                        .fa-angle-double-right:before {
                        content: "\f101";
                        }
                        .fa-angle-double-up:before {
                        content: "\f102";
                        }
                        .fa-angle-double-down:before {
                        content: "\f103";
                        }
                        .fa-angle-left:before {
                        content: "\f104";
                        }
                        .fa-angle-right:before {
                        content: "\f105";
                        }
                        .fa-angle-up:before {
                        content: "\f106";
                        }
                        .fa-angle-down:before {
                        content: "\f107";
                        }
                        .fa-desktop:before {
                        content: "\f108";
                        }
                        .fa-laptop:before {
                        content: "\f109";
                        }
                        .fa-tablet:before {
                        content: "\f10a";
                        }
                        .fa-mobile-phone:before,
                        .fa-mobile:before {
                        content: "\f10b";
                        }
                        .fa-circle-o:before {
                        content: "\f10c";
                        }
                        .fa-quote-left:before {
                        content: "\f10d";
                        }
                        .fa-quote-right:before {
                        content: "\f10e";
                        }
                        .fa-spinner:before {
                        content: "\f110";
                        }
                        .fa-circle:before {
                        content: "\f111";
                        }
                        .fa-mail-reply:before,
                        .fa-reply:before {
                        content: "\f112";
                        }
                        .fa-github-alt:before {
                        content: "\f113";
                        }
                        .fa-folder-o:before {
                        content: "\f114";
                        }
                        .fa-folder-open-o:before {
                        content: "\f115";
                        }
                        .fa-smile-o:before {
                        content: "\f118";
                        }
                        .fa-frown-o:before {
                        content: "\f119";
                        }
                        .fa-meh-o:before {
                        content: "\f11a";
                        }
                        .fa-gamepad:before {
                        content: "\f11b";
                        }
                        .fa-keyboard-o:before {
                        content: "\f11c";
                        }
                        .fa-flag-o:before {
                        content: "\f11d";
                        }
                        .fa-flag-checkered:before {
                        content: "\f11e";
                        }
                        .fa-terminal:before {
                        content: "\f120";
                        }
                        .fa-code:before {
                        content: "\f121";
                        }
                        .fa-mail-reply-all:before,
                        .fa-reply-all:before {
                        content: "\f122";
                        }
                        .fa-star-half-empty:before,
                        .fa-star-half-full:before,
                        .fa-star-half-o:before {
                        content: "\f123";
                        }
                        .fa-location-arrow:before {
                        content: "\f124";
                        }
                        .fa-crop:before {
                        content: "\f125";
                        }
                        .fa-code-fork:before {
                        content: "\f126";
                        }
                        .fa-chain-broken:before,
                        .fa-unlink:before {
                        content: "\f127";
                        }
                        .fa-question:before {
                        content: "\f128";
                        }
                        .fa-info:before {
                        content: "\f129";
                        }
                        .fa-exclamation:before {
                        content: "\f12a";
                        }
                        .fa-superscript:before {
                        content: "\f12b";
                        }
                        .fa-subscript:before {
                        content: "\f12c";
                        }
                        .fa-eraser:before {
                        content: "\f12d";
                        }
                        .fa-puzzle-piece:before {
                        content: "\f12e";
                        }
                        .fa-microphone:before {
                        content: "\f130";
                        }
                        .fa-microphone-slash:before {
                        content: "\f131";
                        }
                        .fa-shield:before {
                        content: "\f132";
                        }
                        .fa-calendar-o:before {
                        content: "\f133";
                        }
                        .fa-fire-extinguisher:before {
                        content: "\f134";
                        }
                        .fa-rocket:before {
                        content: "\f135";
                        }
                        .fa-maxcdn:before {
                        content: "\f136";
                        }
                        .fa-chevron-circle-left:before {
                        content: "\f137";
                        }
                        .fa-chevron-circle-right:before {
                        content: "\f138";
                        }
                        .fa-chevron-circle-up:before {
                        content: "\f139";
                        }
                        .fa-chevron-circle-down:before {
                        content: "\f13a";
                        }
                        .fa-html5:before {
                        content: "\f13b";
                        }
                        .fa-css3:before {
                        content: "\f13c";
                        }
                        .fa-anchor:before {
                        content: "\f13d";
                        }
                        .fa-unlock-alt:before {
                        content: "\f13e";
                        }
                        .fa-bullseye:before {
                        content: "\f140";
                        }
                        .fa-ellipsis-h:before {
                        content: "\f141";
                        }
                        .fa-ellipsis-v:before {
                        content: "\f142";
                        }
                        .fa-rss-square:before {
                        content: "\f143";
                        }
                        .fa-play-circle:before {
                        content: "\f144";
                        }
                        .fa-ticket:before {
                        content: "\f145";
                        }
                        .fa-minus-square:before {
                        content: "\f146";
                        }
                        .fa-minus-square-o:before {
                        content: "\f147";
                        }
                        .fa-level-up:before {
                        content: "\f148";
                        }
                        .fa-level-down:before {
                        content: "\f149";
                        }
                        .fa-check-square:before {
                        content: "\f14a";
                        }
                        .fa-pencil-square:before {
                        content: "\f14b";
                        }
                        .fa-external-link-square:before {
                        content: "\f14c";
                        }
                        .fa-share-square:before {
                        content: "\f14d";
                        }
                        .fa-compass:before {
                        content: "\f14e";
                        }
                        .fa-caret-square-o-down:before,
                        .fa-toggle-down:before {
                        content: "\f150";
                        }
                        .fa-caret-square-o-up:before,
                        .fa-toggle-up:before {
                        content: "\f151";
                        }
                        .fa-caret-square-o-right:before,
                        .fa-toggle-right:before {
                        content: "\f152";
                        }
                        .fa-eur:before,
                        .fa-euro:before {
                        content: "\f153";
                        }
                        .fa-gbp:before {
                        content: "\f154";
                        }
                        .fa-dollar:before,
                        .fa-usd:before {
                        content: "\f155";
                        }
                        .fa-inr:before,
                        .fa-rupee:before {
                        content: "\f156";
                        }
                        .fa-cny:before,
                        .fa-jpy:before,
                        .fa-rmb:before,
                        .fa-yen:before {
                        content: "\f157";
                        }
                        .fa-rouble:before,
                        .fa-rub:before,
                        .fa-ruble:before {
                        content: "\f158";
                        }
                        .fa-krw:before,
                        .fa-won:before {
                        content: "\f159";
                        }
                        .fa-bitcoin:before,
                        .fa-btc:before {
                        content: "\f15a";
                        }
                        .fa-file:before {
                        content: "\f15b";
                        }
                        .fa-file-text:before {
                        content: "\f15c";
                        }
                        .fa-sort-alpha-asc:before {
                        content: "\f15d";
                        }
                        .fa-sort-alpha-desc:before {
                        content: "\f15e";
                        }
                        .fa-sort-amount-asc:before {
                        content: "\f160";
                        }
                        .fa-sort-amount-desc:before {
                        content: "\f161";
                        }
                        .fa-sort-numeric-asc:before {
                        content: "\f162";
                        }
                        .fa-sort-numeric-desc:before {
                        content: "\f163";
                        }
                        .fa-thumbs-up:before {
                        content: "\f164";
                        }
                        .fa-thumbs-down:before {
                        content: "\f165";
                        }
                        .fa-youtube-square:before {
                        content: "\f166";
                        }
                        .fa-youtube:before {
                        content: "\f167";
                        }
                        .fa-xing:before {
                        content: "\f168";
                        }
                        .fa-xing-square:before {
                        content: "\f169";
                        }
                        .fa-youtube-play:before {
                        content: "\f16a";
                        }
                        .fa-dropbox:before {
                        content: "\f16b";
                        }
                        .fa-stack-overflow:before {
                        content: "\f16c";
                        }
                        .fa-instagram:before {
                        content: "\f16d";
                        }
                        .fa-flickr:before {
                        content: "\f16e";
                        }
                        .fa-adn:before {
                        content: "\f170";
                        }
                        .fa-bitbucket:before {
                        content: "\f171";
                        }
                        .fa-bitbucket-square:before {
                        content: "\f172";
                        }
                        .fa-tumblr:before {
                        content: "\f173";
                        }
                        .fa-tumblr-square:before {
                        content: "\f174";
                        }
                        .fa-long-arrow-down:before {
                        content: "\f175";
                        }
                        .fa-long-arrow-up:before {
                        content: "\f176";
                        }
                        .fa-long-arrow-left:before {
                        content: "\f177";
                        }
                        .fa-long-arrow-right:before {
                        content: "\f178";
                        }
                        .fa-apple:before {
                        content: "\f179";
                        }
                        .fa-windows:before {
                        content: "\f17a";
                        }
                        .fa-android:before {
                        content: "\f17b";
                        }
                        .fa-linux:before {
                        content: "\f17c";
                        }
                        .fa-dribbble:before {
                        content: "\f17d";
                        }
                        .fa-skype:before {
                        content: "\f17e";
                        }
                        .fa-foursquare:before {
                        content: "\f180";
                        }
                        .fa-trello:before {
                        content: "\f181";
                        }
                        .fa-female:before {
                        content: "\f182";
                        }
                        .fa-male:before {
                        content: "\f183";
                        }
                        .fa-gittip:before,
                        .fa-gratipay:before {
                        content: "\f184";
                        }
                        .fa-sun-o:before {
                        content: "\f185";
                        }
                        .fa-moon-o:before {
                        content: "\f186";
                        }
                        .fa-archive:before {
                        content: "\f187";
                        }
                        .fa-bug:before {
                        content: "\f188";
                        }
                        .fa-vk:before {
                        content: "\f189";
                        }
                        .fa-weibo:before {
                        content: "\f18a";
                        }
                        .fa-renren:before {
                        content: "\f18b";
                        }
                        .fa-pagelines:before {
                        content: "\f18c";
                        }
                        .fa-stack-exchange:before {
                        content: "\f18d";
                        }
                        .fa-arrow-circle-o-right:before {
                        content: "\f18e";
                        }
                        .fa-arrow-circle-o-left:before {
                        content: "\f190";
                        }
                        .fa-caret-square-o-left:before,
                        .fa-toggle-left:before {
                        content: "\f191";
                        }
                        .fa-dot-circle-o:before {
                        content: "\f192";
                        }
                        .fa-wheelchair:before {
                        content: "\f193";
                        }
                        .fa-vimeo-square:before {
                        content: "\f194";
                        }
                        .fa-try:before,
                        .fa-turkish-lira:before {
                        content: "\f195";
                        }
                        .fa-plus-square-o:before {
                        content: "\f196";
                        }
                        .fa-space-shuttle:before {
                        content: "\f197";
                        }
                        .fa-slack:before {
                        content: "\f198";
                        }
                        .fa-envelope-square:before {
                        content: "\f199";
                        }
                        .fa-wordpress:before {
                        content: "\f19a";
                        }
                        .fa-openid:before {
                        content: "\f19b";
                        }
                        .fa-bank:before,
                        .fa-institution:before,
                        .fa-university:before {
                        content: "\f19c";
                        }
                        .fa-graduation-cap:before,
                        .fa-mortar-board:before {
                        content: "\f19d";
                        }
                        .fa-yahoo:before {
                        content: "\f19e";
                        }
                        .fa-google:before {
                        content: "\f1a0";
                        }
                        .fa-reddit:before {
                        content: "\f1a1";
                        }
                        .fa-reddit-square:before {
                        content: "\f1a2";
                        }
                        .fa-stumbleupon-circle:before {
                        content: "\f1a3";
                        }
                        .fa-stumbleupon:before {
                        content: "\f1a4";
                        }
                        .fa-delicious:before {
                        content: "\f1a5";
                        }
                        .fa-digg:before {
                        content: "\f1a6";
                        }
                        .fa-pied-piper-pp:before {
                        content: "\f1a7";
                        }
                        .fa-pied-piper-alt:before {
                        content: "\f1a8";
                        }
                        .fa-drupal:before {
                        content: "\f1a9";
                        }
                        .fa-joomla:before {
                        content: "\f1aa";
                        }
                        .fa-language:before {
                        content: "\f1ab";
                        }
                        .fa-fax:before {
                        content: "\f1ac";
                        }
                        .fa-building:before {
                        content: "\f1ad";
                        }
                        .fa-child:before {
                        content: "\f1ae";
                        }
                        .fa-paw:before {
                        content: "\f1b0";
                        }
                        .fa-spoon:before {
                        content: "\f1b1";
                        }
                        .fa-cube:before {
                        content: "\f1b2";
                        }
                        .fa-cubes:before {
                        content: "\f1b3";
                        }
                        .fa-behance:before {
                        content: "\f1b4";
                        }
                        .fa-behance-square:before {
                        content: "\f1b5";
                        }
                        .fa-steam:before {
                        content: "\f1b6";
                        }
                        .fa-steam-square:before {
                        content: "\f1b7";
                        }
                        .fa-recycle:before {
                        content: "\f1b8";
                        }
                        .fa-automobile:before,
                        .fa-car:before {
                        content: "\f1b9";
                        }
                        .fa-cab:before,
                        .fa-taxi:before {
                        content: "\f1ba";
                        }
                        .fa-tree:before {
                        content: "\f1bb";
                        }
                        .fa-spotify:before {
                        content: "\f1bc";
                        }
                        .fa-deviantart:before {
                        content: "\f1bd";
                        }
                        .fa-soundcloud:before {
                        content: "\f1be";
                        }
                        .fa-database:before {
                        content: "\f1c0";
                        }
                        .fa-file-pdf-o:before {
                        content: "\f1c1";
                        }
                        .fa-file-word-o:before {
                        content: "\f1c2";
                        }
                        .fa-file-excel-o:before {
                        content: "\f1c3";
                        }
                        .fa-file-powerpoint-o:before {
                        content: "\f1c4";
                        }
                        .fa-file-image-o:before,
                        .fa-file-photo-o:before,
                        .fa-file-picture-o:before {
                        content: "\f1c5";
                        }
                        .fa-file-archive-o:before,
                        .fa-file-zip-o:before {
                        content: "\f1c6";
                        }
                        .fa-file-audio-o:before,
                        .fa-file-sound-o:before {
                        content: "\f1c7";
                        }
                        .fa-file-movie-o:before,
                        .fa-file-video-o:before {
                        content: "\f1c8";
                        }
                        .fa-file-code-o:before {
                        content: "\f1c9";
                        }
                        .fa-vine:before {
                        content: "\f1ca";
                        }
                        .fa-codepen:before {
                        content: "\f1cb";
                        }
                        .fa-jsfiddle:before {
                        content: "\f1cc";
                        }
                        .fa-life-bouy:before,
                        .fa-life-buoy:before,
                        .fa-life-ring:before,
                        .fa-life-saver:before,
                        .fa-support:before {
                        content: "\f1cd";
                        }
                        .fa-circle-o-notch:before {
                        content: "\f1ce";
                        }
                        .fa-ra:before,
                        .fa-rebel:before,
                        .fa-resistance:before {
                        content: "\f1d0";
                        }
                        .fa-empire:before,
                        .fa-ge:before {
                        content: "\f1d1";
                        }
                        .fa-git-square:before {
                        content: "\f1d2";
                        }
                        .fa-git:before {
                        content: "\f1d3";
                        }
                        .fa-hacker-news:before,
                        .fa-y-combinator-square:before,
                        .fa-yc-square:before {
                        content: "\f1d4";
                        }
                        .fa-tencent-weibo:before {
                        content: "\f1d5";
                        }
                        .fa-qq:before {
                        content: "\f1d6";
                        }
                        .fa-wechat:before,
                        .fa-weixin:before {
                        content: "\f1d7";
                        }
                        .fa-paper-plane:before,
                        .fa-send:before {
                        content: "\f1d8";
                        }
                        .fa-paper-plane-o:before,
                        .fa-send-o:before {
                        content: "\f1d9";
                        }
                        .fa-history:before {
                        content: "\f1da";
                        }
                        .fa-circle-thin:before {
                        content: "\f1db";
                        }
                        .fa-header:before {
                        content: "\f1dc";
                        }
                        .fa-paragraph:before {
                        content: "\f1dd";
                        }
                        .fa-sliders:before {
                        content: "\f1de";
                        }
                        .fa-share-alt:before {
                        content: "\f1e0";
                        }
                        .fa-share-alt-square:before {
                        content: "\f1e1";
                        }
                        .fa-bomb:before {
                        content: "\f1e2";
                        }
                        .fa-futbol-o:before,
                        .fa-soccer-ball-o:before {
                        content: "\f1e3";
                        }
                        .fa-tty:before {
                        content: "\f1e4";
                        }
                        .fa-binoculars:before {
                        content: "\f1e5";
                        }
                        .fa-plug:before {
                        content: "\f1e6";
                        }
                        .fa-slideshare:before {
                        content: "\f1e7";
                        }
                        .fa-twitch:before {
                        content: "\f1e8";
                        }
                        .fa-yelp:before {
                        content: "\f1e9";
                        }
                        .fa-newspaper-o:before {
                        content: "\f1ea";
                        }
                        .fa-wifi:before {
                        content: "\f1eb";
                        }
                        .fa-calculator:before {
                        content: "\f1ec";
                        }
                        .fa-paypal:before {
                        content: "\f1ed";
                        }
                        .fa-google-wallet:before {
                        content: "\f1ee";
                        }
                        .fa-cc-visa:before {
                        content: "\f1f0";
                        }
                        .fa-cc-mastercard:before {
                        content: "\f1f1";
                        }
                        .fa-cc-discover:before {
                        content: "\f1f2";
                        }
                        .fa-cc-amex:before {
                        content: "\f1f3";
                        }
                        .fa-cc-paypal:before {
                        content: "\f1f4";
                        }
                        .fa-cc-stripe:before {
                        content: "\f1f5";
                        }
                        .fa-bell-slash:before {
                        content: "\f1f6";
                        }
                        .fa-bell-slash-o:before {
                        content: "\f1f7";
                        }
                        .fa-trash:before {
                        content: "\f1f8";
                        }
                        .fa-copyright:before {
                        content: "\f1f9";
                        }
                        .fa-at:before {
                        content: "\f1fa";
                        }
                        .fa-eyedropper:before {
                        content: "\f1fb";
                        }
                        .fa-paint-brush:before {
                        content: "\f1fc";
                        }
                        .fa-birthday-cake:before {
                        content: "\f1fd";
                        }
                        .fa-area-chart:before {
                        content: "\f1fe";
                        }
                        .fa-pie-chart:before {
                        content: "\f200";
                        }
                        .fa-line-chart:before {
                        content: "\f201";
                        }
                        .fa-lastfm:before {
                        content: "\f202";
                        }
                        .fa-lastfm-square:before {
                        content: "\f203";
                        }
                        .fa-toggle-off:before {
                        content: "\f204";
                        }
                        .fa-toggle-on:before {
                        content: "\f205";
                        }
                        .fa-bicycle:before {
                        content: "\f206";
                        }
                        .fa-bus:before {
                        content: "\f207";
                        }
                        .fa-ioxhost:before {
                        content: "\f208";
                        }
                        .fa-angellist:before {
                        content: "\f209";
                        }
                        .fa-cc:before {
                        content: "\f20a";
                        }
                        .fa-ils:before,
                        .fa-shekel:before,
                        .fa-sheqel:before {
                        content: "\f20b";
                        }
                        .fa-meanpath:before {
                        content: "\f20c";
                        }
                        .fa-buysellads:before {
                        content: "\f20d";
                        }
                        .fa-connectdevelop:before {
                        content: "\f20e";
                        }
                        .fa-dashcube:before {
                        content: "\f210";
                        }
                        .fa-forumbee:before {
                        content: "\f211";
                        }
                        .fa-leanpub:before {
                        content: "\f212";
                        }
                        .fa-sellsy:before {
                        content: "\f213";
                        }
                        .fa-shirtsinbulk:before {
                        content: "\f214";
                        }
                        .fa-simplybuilt:before {
                        content: "\f215";
                        }
                        .fa-skyatlas:before {
                        content: "\f216";
                        }
                        .fa-cart-plus:before {
                        content: "\f217";
                        }
                        .fa-cart-arrow-down:before {
                        content: "\f218";
                        }
                        .fa-diamond:before {
                        content: "\f219";
                        }
                        .fa-ship:before {
                        content: "\f21a";
                        }
                        .fa-user-secret:before {
                        content: "\f21b";
                        }
                        .fa-motorcycle:before {
                        content: "\f21c";
                        }
                        .fa-street-view:before {
                        content: "\f21d";
                        }
                        .fa-heartbeat:before {
                        content: "\f21e";
                        }
                        .fa-venus:before {
                        content: "\f221";
                        }
                        .fa-mars:before {
                        content: "\f222";
                        }
                        .fa-mercury:before {
                        content: "\f223";
                        }
                        .fa-intersex:before,
                        .fa-transgender:before {
                        content: "\f224";
                        }
                        .fa-transgender-alt:before {
                        content: "\f225";
                        }
                        .fa-venus-double:before {
                        content: "\f226";
                        }
                        .fa-mars-double:before {
                        content: "\f227";
                        }
                        .fa-venus-mars:before {
                        content: "\f228";
                        }
                        .fa-mars-stroke:before {
                        content: "\f229";
                        }
                        .fa-mars-stroke-v:before {
                        content: "\f22a";
                        }
                        .fa-mars-stroke-h:before {
                        content: "\f22b";
                        }
                        .fa-neuter:before {
                        content: "\f22c";
                        }
                        .fa-genderless:before {
                        content: "\f22d";
                        }
                        .fa-facebook-official:before {
                        content: "\f230";
                        }
                        .fa-pinterest-p:before {
                        content: "\f231";
                        }
                        .fa-whatsapp:before {
                        content: "\f232";
                        }
                        .fa-server:before {
                        content: "\f233";
                        }
                        .fa-user-plus:before {
                        content: "\f234";
                        }
                        .fa-user-times:before {
                        content: "\f235";
                        }
                        .fa-bed:before,
                        .fa-hotel:before {
                        content: "\f236";
                        }
                        .fa-viacoin:before {
                        content: "\f237";
                        }
                        .fa-train:before {
                        content: "\f238";
                        }
                        .fa-subway:before {
                        content: "\f239";
                        }
                        .fa-medium:before {
                        content: "\f23a";
                        }
                        .fa-y-combinator:before,
                        .fa-yc:before {
                        content: "\f23b";
                        }
                        .fa-optin-monster:before {
                        content: "\f23c";
                        }
                        .fa-opencart:before {
                        content: "\f23d";
                        }
                        .fa-expeditedssl:before {
                        content: "\f23e";
                        }
                        .fa-battery-4:before,
                        .fa-battery-full:before,
                        .fa-battery:before {
                        content: "\f240";
                        }
                        .fa-battery-3:before,
                        .fa-battery-three-quarters:before {
                        content: "\f241";
                        }
                        .fa-battery-2:before,
                        .fa-battery-half:before {
                        content: "\f242";
                        }
                        .fa-battery-1:before,
                        .fa-battery-quarter:before {
                        content: "\f243";
                        }
                        .fa-battery-0:before,
                        .fa-battery-empty:before {
                        content: "\f244";
                        }
                        .fa-mouse-pointer:before {
                        content: "\f245";
                        }
                        .fa-i-cursor:before {
                        content: "\f246";
                        }
                        .fa-object-group:before {
                        content: "\f247";
                        }
                        .fa-object-ungroup:before {
                        content: "\f248";
                        }
                        .fa-sticky-note:before {
                        content: "\f249";
                        }
                        .fa-sticky-note-o:before {
                        content: "\f24a";
                        }
                        .fa-cc-jcb:before {
                        content: "\f24b";
                        }
                        .fa-cc-diners-club:before {
                        content: "\f24c";
                        }
                        .fa-clone:before {
                        content: "\f24d";
                        }
                        .fa-balance-scale:before {
                        content: "\f24e";
                        }
                        .fa-hourglass-o:before {
                        content: "\f250";
                        }
                        .fa-hourglass-1:before,
                        .fa-hourglass-start:before {
                        content: "\f251";
                        }
                        .fa-hourglass-2:before,
                        .fa-hourglass-half:before {
                        content: "\f252";
                        }
                        .fa-hourglass-3:before,
                        .fa-hourglass-end:before {
                        content: "\f253";
                        }
                        .fa-hourglass:before {
                        content: "\f254";
                        }
                        .fa-hand-grab-o:before,
                        .fa-hand-rock-o:before {
                        content: "\f255";
                        }
                        .fa-hand-paper-o:before,
                        .fa-hand-stop-o:before {
                        content: "\f256";
                        }
                        .fa-hand-scissors-o:before {
                        content: "\f257";
                        }
                        .fa-hand-lizard-o:before {
                        content: "\f258";
                        }
                        .fa-hand-spock-o:before {
                        content: "\f259";
                        }
                        .fa-hand-pointer-o:before {
                        content: "\f25a";
                        }
                        .fa-hand-peace-o:before {
                        content: "\f25b";
                        }
                        .fa-trademark:before {
                        content: "\f25c";
                        }
                        .fa-registered:before {
                        content: "\f25d";
                        }
                        .fa-creative-commons:before {
                        content: "\f25e";
                        }
                        .fa-gg:before {
                        content: "\f260";
                        }
                        .fa-gg-circle:before {
                        content: "\f261";
                        }
                        .fa-tripadvisor:before {
                        content: "\f262";
                        }
                        .fa-odnoklassniki:before {
                        content: "\f263";
                        }
                        .fa-odnoklassniki-square:before {
                        content: "\f264";
                        }
                        .fa-get-pocket:before {
                        content: "\f265";
                        }
                        .fa-wikipedia-w:before {
                        content: "\f266";
                        }
                        .fa-safari:before {
                        content: "\f267";
                        }
                        .fa-chrome:before {
                        content: "\f268";
                        }
                        .fa-firefox:before {
                        content: "\f269";
                        }
                        .fa-opera:before {
                        content: "\f26a";
                        }
                        .fa-internet-explorer:before {
                        content: "\f26b";
                        }
                        .fa-television:before,
                        .fa-tv:before {
                        content: "\f26c";
                        }
                        .fa-contao:before {
                        content: "\f26d";
                        }
                        .fa-500px:before {
                        content: "\f26e";
                        }
                        .fa-amazon:before {
                        content: "\f270";
                        }
                        .fa-calendar-plus-o:before {
                        content: "\f271";
                        }
                        .fa-calendar-minus-o:before {
                        content: "\f272";
                        }
                        .fa-calendar-times-o:before {
                        content: "\f273";
                        }
                        .fa-calendar-check-o:before {
                        content: "\f274";
                        }
                        .fa-industry:before {
                        content: "\f275";
                        }
                        .fa-map-pin:before {
                        content: "\f276";
                        }
                        .fa-map-signs:before {
                        content: "\f277";
                        }
                        .fa-map-o:before {
                        content: "\f278";
                        }
                        .fa-map:before {
                        content: "\f279";
                        }
                        .fa-commenting:before {
                        content: "\f27a";
                        }
                        .fa-commenting-o:before {
                        content: "\f27b";
                        }
                        .fa-houzz:before {
                        content: "\f27c";
                        }
                        .fa-vimeo:before {
                        content: "\f27d";
                        }
                        .fa-black-tie:before {
                        content: "\f27e";
                        }
                        .fa-fonticons:before {
                        content: "\f280";
                        }
                        .fa-reddit-alien:before {
                        content: "\f281";
                        }
                        .fa-edge:before {
                        content: "\f282";
                        }
                        .fa-credit-card-alt:before {
                        content: "\f283";
                        }
                        .fa-codiepie:before {
                        content: "\f284";
                        }
                        .fa-modx:before {
                        content: "\f285";
                        }
                        .fa-fort-awesome:before {
                        content: "\f286";
                        }
                        .fa-usb:before {
                        content: "\f287";
                        }
                        .fa-product-hunt:before {
                        content: "\f288";
                        }
                        .fa-mixcloud:before {
                        content: "\f289";
                        }
                        .fa-scribd:before {
                        content: "\f28a";
                        }
                        .fa-pause-circle:before {
                        content: "\f28b";
                        }
                        .fa-pause-circle-o:before {
                        content: "\f28c";
                        }
                        .fa-stop-circle:before {
                        content: "\f28d";
                        }
                        .fa-stop-circle-o:before {
                        content: "\f28e";
                        }
                        .fa-shopping-bag:before {
                        content: "\f290";
                        }
                        .fa-shopping-basket:before {
                        content: "\f291";
                        }
                        .fa-hashtag:before {
                        content: "\f292";
                        }
                        .fa-bluetooth:before {
                        content: "\f293";
                        }
                        .fa-bluetooth-b:before {
                        content: "\f294";
                        }
                        .fa-percent:before {
                        content: "\f295";
                        }
                        .fa-gitlab:before {
                        content: "\f296";
                        }
                        .fa-wpbeginner:before {
                        content: "\f297";
                        }
                        .fa-wpforms:before {
                        content: "\f298";
                        }
                        .fa-envira:before {
                        content: "\f299";
                        }
                        .fa-universal-access:before {
                        content: "\f29a";
                        }
                        .fa-wheelchair-alt:before {
                        content: "\f29b";
                        }
                        .fa-question-circle-o:before {
                        content: "\f29c";
                        }
                        .fa-blind:before {
                        content: "\f29d";
                        }
                        .fa-audio-description:before {
                        content: "\f29e";
                        }
                        .fa-volume-control-phone:before {
                        content: "\f2a0";
                        }
                        .fa-braille:before {
                        content: "\f2a1";
                        }
                        .fa-assistive-listening-systems:before {
                        content: "\f2a2";
                        }
                        .fa-american-sign-language-interpreting:before,
                        .fa-asl-interpreting:before {
                        content: "\f2a3";
                        }
                        .fa-deaf:before,
                        .fa-deafness:before,
                        .fa-hard-of-hearing:before {
                        content: "\f2a4";
                        }
                        .fa-glide:before {
                        content: "\f2a5";
                        }
                        .fa-glide-g:before {
                        content: "\f2a6";
                        }
                        .fa-sign-language:before,
                        .fa-signing:before {
                        content: "\f2a7";
                        }
                        .fa-low-vision:before {
                        content: "\f2a8";
                        }
                        .fa-viadeo:before {
                        content: "\f2a9";
                        }
                        .fa-viadeo-square:before {
                        content: "\f2aa";
                        }
                        .fa-snapchat:before {
                        content: "\f2ab";
                        }
                        .fa-snapchat-ghost:before {
                        content: "\f2ac";
                        }
                        .fa-snapchat-square:before {
                        content: "\f2ad";
                        }
                        .fa-pied-piper:before {
                        content: "\f2ae";
                        }
                        .fa-first-order:before {
                        content: "\f2b0";
                        }
                        .fa-yoast:before {
                        content: "\f2b1";
                        }
                        .fa-themeisle:before {
                        content: "\f2b2";
                        }
                        .fa-google-plus-circle:before,
                        .fa-google-plus-official:before {
                        content: "\f2b3";
                        }
                        .fa-fa:before,
                        .fa-font-awesome:before {
                        content: "\f2b4";
                        }
                        .fa-handshake-o:before {
                        content: "\f2b5";
                        }
                        .fa-envelope-open:before {
                        content: "\f2b6";
                        }
                        .fa-envelope-open-o:before {
                        content: "\f2b7";
                        }
                        .fa-linode:before {
                        content: "\f2b8";
                        }
                        .fa-address-book:before {
                        content: "\f2b9";
                        }
                        .fa-address-book-o:before {
                        content: "\f2ba";
                        }
                        .fa-address-card:before,
                        .fa-vcard:before {
                        content: "\f2bb";
                        }
                        .fa-address-card-o:before,
                        .fa-vcard-o:before {
                        content: "\f2bc";
                        }
                        .fa-user-circle:before {
                        content: "\f2bd";
                        }
                        .fa-user-circle-o:before {
                        content: "\f2be";
                        }
                        .fa-user-o:before {
                        content: "\f2c0";
                        }
                        .fa-id-badge:before {
                        content: "\f2c1";
                        }
                        .fa-drivers-license:before,
                        .fa-id-card:before {
                        content: "\f2c2";
                        }
                        .fa-drivers-license-o:before,
                        .fa-id-card-o:before {
                        content: "\f2c3";
                        }
                        .fa-quora:before {
                        content: "\f2c4";
                        }
                        .fa-free-code-camp:before {
                        content: "\f2c5";
                        }
                        .fa-telegram:before {
                        content: "\f2c6";
                        }
                        .fa-thermometer-4:before,
                        .fa-thermometer-full:before,
                        .fa-thermometer:before {
                        content: "\f2c7";
                        }
                        .fa-thermometer-3:before,
                        .fa-thermometer-three-quarters:before {
                        content: "\f2c8";
                        }
                        .fa-thermometer-2:before,
                        .fa-thermometer-half:before {
                        content: "\f2c9";
                        }
                        .fa-thermometer-1:before,
                        .fa-thermometer-quarter:before {
                        content: "\f2ca";
                        }
                        .fa-thermometer-0:before,
                        .fa-thermometer-empty:before {
                        content: "\f2cb";
                        }
                        .fa-shower:before {
                        content: "\f2cc";
                        }
                        .fa-bath:before,
                        .fa-bathtub:before,
                        .fa-s15:before {
                        content: "\f2cd";
                        }
                        .fa-podcast:before {
                        content: "\f2ce";
                        }
                        .fa-window-maximize:before {
                        content: "\f2d0";
                        }
                        .fa-window-minimize:before {
                        content: "\f2d1";
                        }
                        .fa-window-restore:before {
                        content: "\f2d2";
                        }
                        .fa-times-rectangle:before,
                        .fa-window-close:before {
                        content: "\f2d3";
                        }
                        .fa-times-rectangle-o:before,
                        .fa-window-close-o:before {
                        content: "\f2d4";
                        }
                        .fa-bandcamp:before {
                        content: "\f2d5";
                        }
                        .fa-grav:before {
                        content: "\f2d6";
                        }
                        .fa-etsy:before {
                        content: "\f2d7";
                        }
                        .fa-imdb:before {
                        content: "\f2d8";
                        }
                        .fa-ravelry:before {
                        content: "\f2d9";
                        }
                        .fa-eercast:before {
                        content: "\f2da";
                        }
                        .fa-microchip:before {
                        content: "\f2db";
                        }
                        .fa-snowflake-o:before {
                        content: "\f2dc";
                        }
                        .fa-superpowers:before {
                        content: "\f2dd";
                        }
                        .fa-wpexplorer:before {
                        content: "\f2de";
                        }
                        .fa-meetup:before {
                        content: "\f2e0";
                        }
                        .sr-only {
                        position: absolute;
                        width: 1px;
                        height: 1px;
                        padding: 0;
                        margin: -1px;
                        overflow: hidden;
                        clip: rect(0, 0, 0, 0);
                        border: 0;
                        }
                        .sr-only-focusable:active,
                        .sr-only-focusable:focus {
                        position: static;
                        width: auto;
                        height: auto;
                        margin: 0;
                        overflow: visible;
                        clip: auto;
                        } /*!
                        * animate.css -http://daneden.me/animate
                        * Version - 3.5.0
                        * Licensed under the MIT license - http://opensource.org/licenses/MIT
                        *
                        * Copyright (c) 2016 Daniel Eden
                        */
                        .animated {
                        -webkit-animation-duration: 1s;
                        animation-duration: 1s;
                        -webkit-animation-fill-mode: both;
                        animation-fill-mode: both;
                        }
                        .animated.infinite {
                        -webkit-animation-iteration-count: infinite;
                        animation-iteration-count: infinite;
                        }
                        .animated.hinge {
                        -webkit-animation-duration: 2s;
                        animation-duration: 2s;
                        }
                        .animated.bounceIn,
                        .animated.bounceOut,
                        .animated.flipOutX,
                        .animated.flipOutY {
                        -webkit-animation-duration: 0.75s;
                        animation-duration: 0.75s;
                        }
                        @-webkit-keyframes bounce {
                        0%,
                        20%,
                        53%,
                        80%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        40%,
                        43% {
                            -webkit-transform: translate3d(0, -30px, 0);
                            transform: translate3d(0, -30px, 0);
                        }
                        40%,
                        43%,
                        70% {
                            -webkit-animation-timing-function: cubic-bezier(0.755, 0.05, 0.855, 0.06);
                            animation-timing-function: cubic-bezier(0.755, 0.05, 0.855, 0.06);
                        }
                        70% {
                            -webkit-transform: translate3d(0, -15px, 0);
                            transform: translate3d(0, -15px, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(0, -4px, 0);
                            transform: translate3d(0, -4px, 0);
                        }
                        }
                        @keyframes bounce {
                        0%,
                        20%,
                        53%,
                        80%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        40%,
                        43% {
                            -webkit-transform: translate3d(0, -30px, 0);
                            transform: translate3d(0, -30px, 0);
                        }
                        40%,
                        43%,
                        70% {
                            -webkit-animation-timing-function: cubic-bezier(0.755, 0.05, 0.855, 0.06);
                            animation-timing-function: cubic-bezier(0.755, 0.05, 0.855, 0.06);
                        }
                        70% {
                            -webkit-transform: translate3d(0, -15px, 0);
                            transform: translate3d(0, -15px, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(0, -4px, 0);
                            transform: translate3d(0, -4px, 0);
                        }
                        }
                        .bounce {
                        -webkit-animation-name: bounce;
                        animation-name: bounce;
                        -webkit-transform-origin: center bottom;
                        transform-origin: center bottom;
                        }
                        @-webkit-keyframes flash {
                        0%,
                        50%,
                        to {
                            opacity: 1;
                        }
                        25%,
                        75% {
                            opacity: 0;
                        }
                        }
                        @keyframes flash {
                        0%,
                        50%,
                        to {
                            opacity: 1;
                        }
                        25%,
                        75% {
                            opacity: 0;
                        }
                        }
                        .flash {
                        -webkit-animation-name: flash;
                        animation-name: flash;
                        }
                        @-webkit-keyframes pulse {
                        0% {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        50% {
                            -webkit-transform: scale3d(1.05, 1.05, 1.05);
                            transform: scale3d(1.05, 1.05, 1.05);
                        }
                        to {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        }
                        @keyframes pulse {
                        0% {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        50% {
                            -webkit-transform: scale3d(1.05, 1.05, 1.05);
                            transform: scale3d(1.05, 1.05, 1.05);
                        }
                        to {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        }
                        .pulse {
                        -webkit-animation-name: pulse;
                        animation-name: pulse;
                        }
                        @-webkit-keyframes rubberBand {
                        0% {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        30% {
                            -webkit-transform: scale3d(1.25, 0.75, 1);
                            transform: scale3d(1.25, 0.75, 1);
                        }
                        40% {
                            -webkit-transform: scale3d(0.75, 1.25, 1);
                            transform: scale3d(0.75, 1.25, 1);
                        }
                        50% {
                            -webkit-transform: scale3d(1.15, 0.85, 1);
                            transform: scale3d(1.15, 0.85, 1);
                        }
                        65% {
                            -webkit-transform: scale3d(0.95, 1.05, 1);
                            transform: scale3d(0.95, 1.05, 1);
                        }
                        75% {
                            -webkit-transform: scale3d(1.05, 0.95, 1);
                            transform: scale3d(1.05, 0.95, 1);
                        }
                        to {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        }
                        @keyframes rubberBand {
                        0% {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        30% {
                            -webkit-transform: scale3d(1.25, 0.75, 1);
                            transform: scale3d(1.25, 0.75, 1);
                        }
                        40% {
                            -webkit-transform: scale3d(0.75, 1.25, 1);
                            transform: scale3d(0.75, 1.25, 1);
                        }
                        50% {
                            -webkit-transform: scale3d(1.15, 0.85, 1);
                            transform: scale3d(1.15, 0.85, 1);
                        }
                        65% {
                            -webkit-transform: scale3d(0.95, 1.05, 1);
                            transform: scale3d(0.95, 1.05, 1);
                        }
                        75% {
                            -webkit-transform: scale3d(1.05, 0.95, 1);
                            transform: scale3d(1.05, 0.95, 1);
                        }
                        to {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        }
                        .rubberBand {
                        -webkit-animation-name: rubberBand;
                        animation-name: rubberBand;
                        }
                        @-webkit-keyframes shake {
                        0%,
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        10%,
                        30%,
                        50%,
                        70%,
                        90% {
                            -webkit-transform: translate3d(-10px, 0, 0);
                            transform: translate3d(-10px, 0, 0);
                        }
                        20%,
                        40%,
                        60%,
                        80% {
                            -webkit-transform: translate3d(10px, 0, 0);
                            transform: translate3d(10px, 0, 0);
                        }
                        }
                        @keyframes shake {
                        0%,
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        10%,
                        30%,
                        50%,
                        70%,
                        90% {
                            -webkit-transform: translate3d(-10px, 0, 0);
                            transform: translate3d(-10px, 0, 0);
                        }
                        20%,
                        40%,
                        60%,
                        80% {
                            -webkit-transform: translate3d(10px, 0, 0);
                            transform: translate3d(10px, 0, 0);
                        }
                        }
                        .shake {
                        -webkit-animation-name: shake;
                        animation-name: shake;
                        }
                        @-webkit-keyframes headShake {
                        0% {
                            -webkit-transform: translateX(0);
                            transform: translateX(0);
                        }
                        6.5% {
                            -webkit-transform: translateX(-6px) rotateY(-9deg);
                            transform: translateX(-6px) rotateY(-9deg);
                        }
                        18.5% {
                            -webkit-transform: translateX(5px) rotateY(7deg);
                            transform: translateX(5px) rotateY(7deg);
                        }
                        31.5% {
                            -webkit-transform: translateX(-3px) rotateY(-5deg);
                            transform: translateX(-3px) rotateY(-5deg);
                        }
                        43.5% {
                            -webkit-transform: translateX(2px) rotateY(3deg);
                            transform: translateX(2px) rotateY(3deg);
                        }
                        50% {
                            -webkit-transform: translateX(0);
                            transform: translateX(0);
                        }
                        }
                        @keyframes headShake {
                        0% {
                            -webkit-transform: translateX(0);
                            transform: translateX(0);
                        }
                        6.5% {
                            -webkit-transform: translateX(-6px) rotateY(-9deg);
                            transform: translateX(-6px) rotateY(-9deg);
                        }
                        18.5% {
                            -webkit-transform: translateX(5px) rotateY(7deg);
                            transform: translateX(5px) rotateY(7deg);
                        }
                        31.5% {
                            -webkit-transform: translateX(-3px) rotateY(-5deg);
                            transform: translateX(-3px) rotateY(-5deg);
                        }
                        43.5% {
                            -webkit-transform: translateX(2px) rotateY(3deg);
                            transform: translateX(2px) rotateY(3deg);
                        }
                        50% {
                            -webkit-transform: translateX(0);
                            transform: translateX(0);
                        }
                        }
                        .headShake {
                        -webkit-animation-timing-function: ease-in-out;
                        animation-timing-function: ease-in-out;
                        -webkit-animation-name: headShake;
                        animation-name: headShake;
                        }
                        @-webkit-keyframes swing {
                        20% {
                            -webkit-transform: rotate(15deg);
                            transform: rotate(15deg);
                        }
                        40% {
                            -webkit-transform: rotate(-10deg);
                            transform: rotate(-10deg);
                        }
                        60% {
                            -webkit-transform: rotate(5deg);
                            transform: rotate(5deg);
                        }
                        80% {
                            -webkit-transform: rotate(-5deg);
                            transform: rotate(-5deg);
                        }
                        to {
                            -webkit-transform: rotate(0);
                            transform: rotate(0);
                        }
                        }
                        @keyframes swing {
                        20% {
                            -webkit-transform: rotate(15deg);
                            transform: rotate(15deg);
                        }
                        40% {
                            -webkit-transform: rotate(-10deg);
                            transform: rotate(-10deg);
                        }
                        60% {
                            -webkit-transform: rotate(5deg);
                            transform: rotate(5deg);
                        }
                        80% {
                            -webkit-transform: rotate(-5deg);
                            transform: rotate(-5deg);
                        }
                        to {
                            -webkit-transform: rotate(0);
                            transform: rotate(0);
                        }
                        }
                        .swing {
                        -webkit-transform-origin: top center;
                        transform-origin: top center;
                        -webkit-animation-name: swing;
                        animation-name: swing;
                        }
                        @-webkit-keyframes tada {
                        0% {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        10%,
                        20% {
                            -webkit-transform: scale3d(0.9, 0.9, 0.9) rotate(-3deg);
                            transform: scale3d(0.9, 0.9, 0.9) rotate(-3deg);
                        }
                        30%,
                        50%,
                        70%,
                        90% {
                            -webkit-transform: scale3d(1.1, 1.1, 1.1) rotate(3deg);
                            transform: scale3d(1.1, 1.1, 1.1) rotate(3deg);
                        }
                        40%,
                        60%,
                        80% {
                            -webkit-transform: scale3d(1.1, 1.1, 1.1) rotate(-3deg);
                            transform: scale3d(1.1, 1.1, 1.1) rotate(-3deg);
                        }
                        to {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        }
                        @keyframes tada {
                        0% {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        10%,
                        20% {
                            -webkit-transform: scale3d(0.9, 0.9, 0.9) rotate(-3deg);
                            transform: scale3d(0.9, 0.9, 0.9) rotate(-3deg);
                        }
                        30%,
                        50%,
                        70%,
                        90% {
                            -webkit-transform: scale3d(1.1, 1.1, 1.1) rotate(3deg);
                            transform: scale3d(1.1, 1.1, 1.1) rotate(3deg);
                        }
                        40%,
                        60%,
                        80% {
                            -webkit-transform: scale3d(1.1, 1.1, 1.1) rotate(-3deg);
                            transform: scale3d(1.1, 1.1, 1.1) rotate(-3deg);
                        }
                        to {
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        }
                        .tada {
                        -webkit-animation-name: tada;
                        animation-name: tada;
                        }
                        @-webkit-keyframes wobble {
                        0% {
                            -webkit-transform: none;
                            transform: none;
                        }
                        15% {
                            -webkit-transform: translate3d(-25%, 0, 0) rotate(-5deg);
                            transform: translate3d(-25%, 0, 0) rotate(-5deg);
                        }
                        30% {
                            -webkit-transform: translate3d(20%, 0, 0) rotate(3deg);
                            transform: translate3d(20%, 0, 0) rotate(3deg);
                        }
                        45% {
                            -webkit-transform: translate3d(-15%, 0, 0) rotate(-3deg);
                            transform: translate3d(-15%, 0, 0) rotate(-3deg);
                        }
                        60% {
                            -webkit-transform: translate3d(10%, 0, 0) rotate(2deg);
                            transform: translate3d(10%, 0, 0) rotate(2deg);
                        }
                        75% {
                            -webkit-transform: translate3d(-5%, 0, 0) rotate(-1deg);
                            transform: translate3d(-5%, 0, 0) rotate(-1deg);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes wobble {
                        0% {
                            -webkit-transform: none;
                            transform: none;
                        }
                        15% {
                            -webkit-transform: translate3d(-25%, 0, 0) rotate(-5deg);
                            transform: translate3d(-25%, 0, 0) rotate(-5deg);
                        }
                        30% {
                            -webkit-transform: translate3d(20%, 0, 0) rotate(3deg);
                            transform: translate3d(20%, 0, 0) rotate(3deg);
                        }
                        45% {
                            -webkit-transform: translate3d(-15%, 0, 0) rotate(-3deg);
                            transform: translate3d(-15%, 0, 0) rotate(-3deg);
                        }
                        60% {
                            -webkit-transform: translate3d(10%, 0, 0) rotate(2deg);
                            transform: translate3d(10%, 0, 0) rotate(2deg);
                        }
                        75% {
                            -webkit-transform: translate3d(-5%, 0, 0) rotate(-1deg);
                            transform: translate3d(-5%, 0, 0) rotate(-1deg);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .wobble {
                        -webkit-animation-name: wobble;
                        animation-name: wobble;
                        }
                        @-webkit-keyframes jello {
                        0%,
                        11.1%,
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        22.2% {
                            -webkit-transform: skewX(-12.5deg) skewY(-12.5deg);
                            transform: skewX(-12.5deg) skewY(-12.5deg);
                        }
                        33.3% {
                            -webkit-transform: skewX(6.25deg) skewY(6.25deg);
                            transform: skewX(6.25deg) skewY(6.25deg);
                        }
                        44.4% {
                            -webkit-transform: skewX(-3.125deg) skewY(-3.125deg);
                            transform: skewX(-3.125deg) skewY(-3.125deg);
                        }
                        55.5% {
                            -webkit-transform: skewX(1.5625deg) skewY(1.5625deg);
                            transform: skewX(1.5625deg) skewY(1.5625deg);
                        }
                        66.6% {
                            -webkit-transform: skewX(-0.78125deg) skewY(-0.78125deg);
                            transform: skewX(-0.78125deg) skewY(-0.78125deg);
                        }
                        77.7% {
                            -webkit-transform: skewX(0.390625deg) skewY(0.390625deg);
                            transform: skewX(0.390625deg) skewY(0.390625deg);
                        }
                        88.8% {
                            -webkit-transform: skewX(-0.1953125deg) skewY(-0.1953125deg);
                            transform: skewX(-0.1953125deg) skewY(-0.1953125deg);
                        }
                        }
                        @keyframes jello {
                        0%,
                        11.1%,
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        22.2% {
                            -webkit-transform: skewX(-12.5deg) skewY(-12.5deg);
                            transform: skewX(-12.5deg) skewY(-12.5deg);
                        }
                        33.3% {
                            -webkit-transform: skewX(6.25deg) skewY(6.25deg);
                            transform: skewX(6.25deg) skewY(6.25deg);
                        }
                        44.4% {
                            -webkit-transform: skewX(-3.125deg) skewY(-3.125deg);
                            transform: skewX(-3.125deg) skewY(-3.125deg);
                        }
                        55.5% {
                            -webkit-transform: skewX(1.5625deg) skewY(1.5625deg);
                            transform: skewX(1.5625deg) skewY(1.5625deg);
                        }
                        66.6% {
                            -webkit-transform: skewX(-0.78125deg) skewY(-0.78125deg);
                            transform: skewX(-0.78125deg) skewY(-0.78125deg);
                        }
                        77.7% {
                            -webkit-transform: skewX(0.390625deg) skewY(0.390625deg);
                            transform: skewX(0.390625deg) skewY(0.390625deg);
                        }
                        88.8% {
                            -webkit-transform: skewX(-0.1953125deg) skewY(-0.1953125deg);
                            transform: skewX(-0.1953125deg) skewY(-0.1953125deg);
                        }
                        }
                        .jello {
                        -webkit-animation-name: jello;
                        animation-name: jello;
                        -webkit-transform-origin: center;
                        transform-origin: center;
                        }
                        @-webkit-keyframes bounceIn {
                        0%,
                        20%,
                        40%,
                        60%,
                        80%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.3, 0.3, 0.3);
                            transform: scale3d(0.3, 0.3, 0.3);
                        }
                        20% {
                            -webkit-transform: scale3d(1.1, 1.1, 1.1);
                            transform: scale3d(1.1, 1.1, 1.1);
                        }
                        40% {
                            -webkit-transform: scale3d(0.9, 0.9, 0.9);
                            transform: scale3d(0.9, 0.9, 0.9);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(1.03, 1.03, 1.03);
                            transform: scale3d(1.03, 1.03, 1.03);
                        }
                        80% {
                            -webkit-transform: scale3d(0.97, 0.97, 0.97);
                            transform: scale3d(0.97, 0.97, 0.97);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        }
                        @keyframes bounceIn {
                        0%,
                        20%,
                        40%,
                        60%,
                        80%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.3, 0.3, 0.3);
                            transform: scale3d(0.3, 0.3, 0.3);
                        }
                        20% {
                            -webkit-transform: scale3d(1.1, 1.1, 1.1);
                            transform: scale3d(1.1, 1.1, 1.1);
                        }
                        40% {
                            -webkit-transform: scale3d(0.9, 0.9, 0.9);
                            transform: scale3d(0.9, 0.9, 0.9);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(1.03, 1.03, 1.03);
                            transform: scale3d(1.03, 1.03, 1.03);
                        }
                        80% {
                            -webkit-transform: scale3d(0.97, 0.97, 0.97);
                            transform: scale3d(0.97, 0.97, 0.97);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: scaleX(1);
                            transform: scaleX(1);
                        }
                        }
                        .bounceIn {
                        -webkit-animation-name: bounceIn;
                        animation-name: bounceIn;
                        }
                        @-webkit-keyframes bounceInDown {
                        0%,
                        60%,
                        75%,
                        90%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -3000px, 0);
                            transform: translate3d(0, -3000px, 0);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: translate3d(0, 25px, 0);
                            transform: translate3d(0, 25px, 0);
                        }
                        75% {
                            -webkit-transform: translate3d(0, -10px, 0);
                            transform: translate3d(0, -10px, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(0, 5px, 0);
                            transform: translate3d(0, 5px, 0);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes bounceInDown {
                        0%,
                        60%,
                        75%,
                        90%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -3000px, 0);
                            transform: translate3d(0, -3000px, 0);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: translate3d(0, 25px, 0);
                            transform: translate3d(0, 25px, 0);
                        }
                        75% {
                            -webkit-transform: translate3d(0, -10px, 0);
                            transform: translate3d(0, -10px, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(0, 5px, 0);
                            transform: translate3d(0, 5px, 0);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .bounceInDown {
                        -webkit-animation-name: bounceInDown;
                        animation-name: bounceInDown;
                        }
                        @-webkit-keyframes bounceInLeft {
                        0%,
                        60%,
                        75%,
                        90%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(-3000px, 0, 0);
                            transform: translate3d(-3000px, 0, 0);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: translate3d(25px, 0, 0);
                            transform: translate3d(25px, 0, 0);
                        }
                        75% {
                            -webkit-transform: translate3d(-10px, 0, 0);
                            transform: translate3d(-10px, 0, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(5px, 0, 0);
                            transform: translate3d(5px, 0, 0);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes bounceInLeft {
                        0%,
                        60%,
                        75%,
                        90%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(-3000px, 0, 0);
                            transform: translate3d(-3000px, 0, 0);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: translate3d(25px, 0, 0);
                            transform: translate3d(25px, 0, 0);
                        }
                        75% {
                            -webkit-transform: translate3d(-10px, 0, 0);
                            transform: translate3d(-10px, 0, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(5px, 0, 0);
                            transform: translate3d(5px, 0, 0);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .bounceInLeft {
                        -webkit-animation-name: bounceInLeft;
                        animation-name: bounceInLeft;
                        }
                        @-webkit-keyframes bounceInRight {
                        0%,
                        60%,
                        75%,
                        90%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(3000px, 0, 0);
                            transform: translate3d(3000px, 0, 0);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: translate3d(-25px, 0, 0);
                            transform: translate3d(-25px, 0, 0);
                        }
                        75% {
                            -webkit-transform: translate3d(10px, 0, 0);
                            transform: translate3d(10px, 0, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(-5px, 0, 0);
                            transform: translate3d(-5px, 0, 0);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes bounceInRight {
                        0%,
                        60%,
                        75%,
                        90%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(3000px, 0, 0);
                            transform: translate3d(3000px, 0, 0);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: translate3d(-25px, 0, 0);
                            transform: translate3d(-25px, 0, 0);
                        }
                        75% {
                            -webkit-transform: translate3d(10px, 0, 0);
                            transform: translate3d(10px, 0, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(-5px, 0, 0);
                            transform: translate3d(-5px, 0, 0);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .bounceInRight {
                        -webkit-animation-name: bounceInRight;
                        animation-name: bounceInRight;
                        }
                        @-webkit-keyframes bounceInUp {
                        0%,
                        60%,
                        75%,
                        90%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 3000px, 0);
                            transform: translate3d(0, 3000px, 0);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: translate3d(0, -20px, 0);
                            transform: translate3d(0, -20px, 0);
                        }
                        75% {
                            -webkit-transform: translate3d(0, 10px, 0);
                            transform: translate3d(0, 10px, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(0, -5px, 0);
                            transform: translate3d(0, -5px, 0);
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        @keyframes bounceInUp {
                        0%,
                        60%,
                        75%,
                        90%,
                        to {
                            -webkit-animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
                        }
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 3000px, 0);
                            transform: translate3d(0, 3000px, 0);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: translate3d(0, -20px, 0);
                            transform: translate3d(0, -20px, 0);
                        }
                        75% {
                            -webkit-transform: translate3d(0, 10px, 0);
                            transform: translate3d(0, 10px, 0);
                        }
                        90% {
                            -webkit-transform: translate3d(0, -5px, 0);
                            transform: translate3d(0, -5px, 0);
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        .bounceInUp {
                        -webkit-animation-name: bounceInUp;
                        animation-name: bounceInUp;
                        }
                        @-webkit-keyframes bounceOut {
                        20% {
                            -webkit-transform: scale3d(0.9, 0.9, 0.9);
                            transform: scale3d(0.9, 0.9, 0.9);
                        }
                        50%,
                        55% {
                            opacity: 1;
                            -webkit-transform: scale3d(1.1, 1.1, 1.1);
                            transform: scale3d(1.1, 1.1, 1.1);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale3d(0.3, 0.3, 0.3);
                            transform: scale3d(0.3, 0.3, 0.3);
                        }
                        }
                        @keyframes bounceOut {
                        20% {
                            -webkit-transform: scale3d(0.9, 0.9, 0.9);
                            transform: scale3d(0.9, 0.9, 0.9);
                        }
                        50%,
                        55% {
                            opacity: 1;
                            -webkit-transform: scale3d(1.1, 1.1, 1.1);
                            transform: scale3d(1.1, 1.1, 1.1);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale3d(0.3, 0.3, 0.3);
                            transform: scale3d(0.3, 0.3, 0.3);
                        }
                        }
                        .bounceOut {
                        -webkit-animation-name: bounceOut;
                        animation-name: bounceOut;
                        }
                        @-webkit-keyframes bounceOutDown {
                        20% {
                            -webkit-transform: translate3d(0, 10px, 0);
                            transform: translate3d(0, 10px, 0);
                        }
                        40%,
                        45% {
                            opacity: 1;
                            -webkit-transform: translate3d(0, -20px, 0);
                            transform: translate3d(0, -20px, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 2000px, 0);
                            transform: translate3d(0, 2000px, 0);
                        }
                        }
                        @keyframes bounceOutDown {
                        20% {
                            -webkit-transform: translate3d(0, 10px, 0);
                            transform: translate3d(0, 10px, 0);
                        }
                        40%,
                        45% {
                            opacity: 1;
                            -webkit-transform: translate3d(0, -20px, 0);
                            transform: translate3d(0, -20px, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 2000px, 0);
                            transform: translate3d(0, 2000px, 0);
                        }
                        }
                        .bounceOutDown {
                        -webkit-animation-name: bounceOutDown;
                        animation-name: bounceOutDown;
                        }
                        @-webkit-keyframes bounceOutLeft {
                        20% {
                            opacity: 1;
                            -webkit-transform: translate3d(20px, 0, 0);
                            transform: translate3d(20px, 0, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(-2000px, 0, 0);
                            transform: translate3d(-2000px, 0, 0);
                        }
                        }
                        @keyframes bounceOutLeft {
                        20% {
                            opacity: 1;
                            -webkit-transform: translate3d(20px, 0, 0);
                            transform: translate3d(20px, 0, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(-2000px, 0, 0);
                            transform: translate3d(-2000px, 0, 0);
                        }
                        }
                        .bounceOutLeft {
                        -webkit-animation-name: bounceOutLeft;
                        animation-name: bounceOutLeft;
                        }
                        @-webkit-keyframes bounceOutRight {
                        20% {
                            opacity: 1;
                            -webkit-transform: translate3d(-20px, 0, 0);
                            transform: translate3d(-20px, 0, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(2000px, 0, 0);
                            transform: translate3d(2000px, 0, 0);
                        }
                        }
                        @keyframes bounceOutRight {
                        20% {
                            opacity: 1;
                            -webkit-transform: translate3d(-20px, 0, 0);
                            transform: translate3d(-20px, 0, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(2000px, 0, 0);
                            transform: translate3d(2000px, 0, 0);
                        }
                        }
                        .bounceOutRight {
                        -webkit-animation-name: bounceOutRight;
                        animation-name: bounceOutRight;
                        }
                        @-webkit-keyframes bounceOutUp {
                        20% {
                            -webkit-transform: translate3d(0, -10px, 0);
                            transform: translate3d(0, -10px, 0);
                        }
                        40%,
                        45% {
                            opacity: 1;
                            -webkit-transform: translate3d(0, 20px, 0);
                            transform: translate3d(0, 20px, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -2000px, 0);
                            transform: translate3d(0, -2000px, 0);
                        }
                        }
                        @keyframes bounceOutUp {
                        20% {
                            -webkit-transform: translate3d(0, -10px, 0);
                            transform: translate3d(0, -10px, 0);
                        }
                        40%,
                        45% {
                            opacity: 1;
                            -webkit-transform: translate3d(0, 20px, 0);
                            transform: translate3d(0, 20px, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -2000px, 0);
                            transform: translate3d(0, -2000px, 0);
                        }
                        }
                        .bounceOutUp {
                        -webkit-animation-name: bounceOutUp;
                        animation-name: bounceOutUp;
                        }
                        @-webkit-keyframes fadeIn {
                        0% {
                            opacity: 0;
                        }
                        to {
                            opacity: 1;
                        }
                        }
                        @keyframes fadeIn {
                        0% {
                            opacity: 0;
                        }
                        to {
                            opacity: 1;
                        }
                        }
                        .fadeIn {
                        -webkit-animation-name: fadeIn;
                        animation-name: fadeIn;
                        }
                        @-webkit-keyframes fadeInDown {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -100%, 0);
                            transform: translate3d(0, -100%, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes fadeInDown {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -100%, 0);
                            transform: translate3d(0, -100%, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .fadeInDown {
                        -webkit-animation-name: fadeInDown;
                        animation-name: fadeInDown;
                        }
                        @-webkit-keyframes fadeInDownBig {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -2000px, 0);
                            transform: translate3d(0, -2000px, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes fadeInDownBig {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -2000px, 0);
                            transform: translate3d(0, -2000px, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .fadeInDownBig {
                        -webkit-animation-name: fadeInDownBig;
                        animation-name: fadeInDownBig;
                        }
                        @-webkit-keyframes fadeInLeft {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes fadeInLeft {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .fadeInLeft {
                        -webkit-animation-name: fadeInLeft;
                        animation-name: fadeInLeft;
                        }
                        @-webkit-keyframes fadeInLeftBig {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(-2000px, 0, 0);
                            transform: translate3d(-2000px, 0, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes fadeInLeftBig {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(-2000px, 0, 0);
                            transform: translate3d(-2000px, 0, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .fadeInLeftBig {
                        -webkit-animation-name: fadeInLeftBig;
                        animation-name: fadeInLeftBig;
                        }
                        @-webkit-keyframes fadeInRight {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes fadeInRight {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .fadeInRight {
                        -webkit-animation-name: fadeInRight;
                        animation-name: fadeInRight;
                        }
                        @-webkit-keyframes fadeInRightBig {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(2000px, 0, 0);
                            transform: translate3d(2000px, 0, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes fadeInRightBig {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(2000px, 0, 0);
                            transform: translate3d(2000px, 0, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .fadeInRightBig {
                        -webkit-animation-name: fadeInRightBig;
                        animation-name: fadeInRightBig;
                        }
                        @-webkit-keyframes fadeInUp {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 100%, 0);
                            transform: translate3d(0, 100%, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes fadeInUp {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 100%, 0);
                            transform: translate3d(0, 100%, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .fadeInUp {
                        -webkit-animation-name: fadeInUp;
                        animation-name: fadeInUp;
                        }
                        @-webkit-keyframes fadeInUpBig {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 2000px, 0);
                            transform: translate3d(0, 2000px, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes fadeInUpBig {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 2000px, 0);
                            transform: translate3d(0, 2000px, 0);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .fadeInUpBig {
                        -webkit-animation-name: fadeInUpBig;
                        animation-name: fadeInUpBig;
                        }
                        @-webkit-keyframes fadeOut {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                        }
                        }
                        @keyframes fadeOut {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                        }
                        }
                        .fadeOut {
                        -webkit-animation-name: fadeOut;
                        animation-name: fadeOut;
                        }
                        @-webkit-keyframes fadeOutDown {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 100%, 0);
                            transform: translate3d(0, 100%, 0);
                        }
                        }
                        @keyframes fadeOutDown {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 100%, 0);
                            transform: translate3d(0, 100%, 0);
                        }
                        }
                        .fadeOutDown {
                        -webkit-animation-name: fadeOutDown;
                        animation-name: fadeOutDown;
                        }
                        @-webkit-keyframes fadeOutDownBig {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 2000px, 0);
                            transform: translate3d(0, 2000px, 0);
                        }
                        }
                        @keyframes fadeOutDownBig {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, 2000px, 0);
                            transform: translate3d(0, 2000px, 0);
                        }
                        }
                        .fadeOutDownBig {
                        -webkit-animation-name: fadeOutDownBig;
                        animation-name: fadeOutDownBig;
                        }
                        @-webkit-keyframes fadeOutLeft {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                        }
                        }
                        @keyframes fadeOutLeft {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                        }
                        }
                        .fadeOutLeft {
                        -webkit-animation-name: fadeOutLeft;
                        animation-name: fadeOutLeft;
                        }
                        @-webkit-keyframes fadeOutLeftBig {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(-2000px, 0, 0);
                            transform: translate3d(-2000px, 0, 0);
                        }
                        }
                        @keyframes fadeOutLeftBig {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(-2000px, 0, 0);
                            transform: translate3d(-2000px, 0, 0);
                        }
                        }
                        .fadeOutLeftBig {
                        -webkit-animation-name: fadeOutLeftBig;
                        animation-name: fadeOutLeftBig;
                        }
                        @-webkit-keyframes fadeOutRight {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                        }
                        }
                        @keyframes fadeOutRight {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                        }
                        }
                        .fadeOutRight {
                        -webkit-animation-name: fadeOutRight;
                        animation-name: fadeOutRight;
                        }
                        @-webkit-keyframes fadeOutRightBig {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(2000px, 0, 0);
                            transform: translate3d(2000px, 0, 0);
                        }
                        }
                        @keyframes fadeOutRightBig {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(2000px, 0, 0);
                            transform: translate3d(2000px, 0, 0);
                        }
                        }
                        .fadeOutRightBig {
                        -webkit-animation-name: fadeOutRightBig;
                        animation-name: fadeOutRightBig;
                        }
                        @-webkit-keyframes fadeOutUp {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -100%, 0);
                            transform: translate3d(0, -100%, 0);
                        }
                        }
                        @keyframes fadeOutUp {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -100%, 0);
                            transform: translate3d(0, -100%, 0);
                        }
                        }
                        .fadeOutUp {
                        -webkit-animation-name: fadeOutUp;
                        animation-name: fadeOutUp;
                        }
                        @-webkit-keyframes fadeOutUpBig {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -2000px, 0);
                            transform: translate3d(0, -2000px, 0);
                        }
                        }
                        @keyframes fadeOutUpBig {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(0, -2000px, 0);
                            transform: translate3d(0, -2000px, 0);
                        }
                        }
                        .fadeOutUpBig {
                        -webkit-animation-name: fadeOutUpBig;
                        animation-name: fadeOutUpBig;
                        }
                        @-webkit-keyframes flip {
                        0% {
                            -webkit-transform: perspective(400px) rotateY(-1turn);
                            transform: perspective(400px) rotateY(-1turn);
                        }
                        0%,
                        40% {
                            -webkit-animation-timing-function: ease-out;
                            animation-timing-function: ease-out;
                        }
                        40% {
                            -webkit-transform: perspective(400px) translateZ(150px) rotateY(-190deg);
                            transform: perspective(400px) translateZ(150px) rotateY(-190deg);
                        }
                        50% {
                            -webkit-transform: perspective(400px) translateZ(150px) rotateY(-170deg);
                            transform: perspective(400px) translateZ(150px) rotateY(-170deg);
                        }
                        50%,
                        80% {
                            -webkit-animation-timing-function: ease-in;
                            animation-timing-function: ease-in;
                        }
                        80% {
                            -webkit-transform: perspective(400px) scale3d(0.95, 0.95, 0.95);
                            transform: perspective(400px) scale3d(0.95, 0.95, 0.95);
                        }
                        to {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                            -webkit-animation-timing-function: ease-in;
                            animation-timing-function: ease-in;
                        }
                        }
                        @keyframes flip {
                        0% {
                            -webkit-transform: perspective(400px) rotateY(-1turn);
                            transform: perspective(400px) rotateY(-1turn);
                        }
                        0%,
                        40% {
                            -webkit-animation-timing-function: ease-out;
                            animation-timing-function: ease-out;
                        }
                        40% {
                            -webkit-transform: perspective(400px) translateZ(150px) rotateY(-190deg);
                            transform: perspective(400px) translateZ(150px) rotateY(-190deg);
                        }
                        50% {
                            -webkit-transform: perspective(400px) translateZ(150px) rotateY(-170deg);
                            transform: perspective(400px) translateZ(150px) rotateY(-170deg);
                        }
                        50%,
                        80% {
                            -webkit-animation-timing-function: ease-in;
                            animation-timing-function: ease-in;
                        }
                        80% {
                            -webkit-transform: perspective(400px) scale3d(0.95, 0.95, 0.95);
                            transform: perspective(400px) scale3d(0.95, 0.95, 0.95);
                        }
                        to {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                            -webkit-animation-timing-function: ease-in;
                            animation-timing-function: ease-in;
                        }
                        }
                        .animated.flip {
                        -webkit-backface-visibility: visible;
                        backface-visibility: visible;
                        -webkit-animation-name: flip;
                        animation-name: flip;
                        }
                        @-webkit-keyframes flipInX {
                        0% {
                            -webkit-transform: perspective(400px) rotateX(90deg);
                            transform: perspective(400px) rotateX(90deg);
                            opacity: 0;
                        }
                        0%,
                        40% {
                            -webkit-animation-timing-function: ease-in;
                            animation-timing-function: ease-in;
                        }
                        40% {
                            -webkit-transform: perspective(400px) rotateX(-20deg);
                            transform: perspective(400px) rotateX(-20deg);
                        }
                        60% {
                            -webkit-transform: perspective(400px) rotateX(10deg);
                            transform: perspective(400px) rotateX(10deg);
                            opacity: 1;
                        }
                        80% {
                            -webkit-transform: perspective(400px) rotateX(-5deg);
                            transform: perspective(400px) rotateX(-5deg);
                        }
                        to {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                        }
                        }
                        @keyframes flipInX {
                        0% {
                            -webkit-transform: perspective(400px) rotateX(90deg);
                            transform: perspective(400px) rotateX(90deg);
                            opacity: 0;
                        }
                        0%,
                        40% {
                            -webkit-animation-timing-function: ease-in;
                            animation-timing-function: ease-in;
                        }
                        40% {
                            -webkit-transform: perspective(400px) rotateX(-20deg);
                            transform: perspective(400px) rotateX(-20deg);
                        }
                        60% {
                            -webkit-transform: perspective(400px) rotateX(10deg);
                            transform: perspective(400px) rotateX(10deg);
                            opacity: 1;
                        }
                        80% {
                            -webkit-transform: perspective(400px) rotateX(-5deg);
                            transform: perspective(400px) rotateX(-5deg);
                        }
                        to {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                        }
                        }
                        .flipInX {
                        -webkit-backface-visibility: visible !important;
                        backface-visibility: visible !important;
                        -webkit-animation-name: flipInX;
                        animation-name: flipInX;
                        }
                        @-webkit-keyframes flipInY {
                        0% {
                            -webkit-transform: perspective(400px) rotateY(90deg);
                            transform: perspective(400px) rotateY(90deg);
                            opacity: 0;
                        }
                        0%,
                        40% {
                            -webkit-animation-timing-function: ease-in;
                            animation-timing-function: ease-in;
                        }
                        40% {
                            -webkit-transform: perspective(400px) rotateY(-20deg);
                            transform: perspective(400px) rotateY(-20deg);
                        }
                        60% {
                            -webkit-transform: perspective(400px) rotateY(10deg);
                            transform: perspective(400px) rotateY(10deg);
                            opacity: 1;
                        }
                        80% {
                            -webkit-transform: perspective(400px) rotateY(-5deg);
                            transform: perspective(400px) rotateY(-5deg);
                        }
                        to {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                        }
                        }
                        @keyframes flipInY {
                        0% {
                            -webkit-transform: perspective(400px) rotateY(90deg);
                            transform: perspective(400px) rotateY(90deg);
                            opacity: 0;
                        }
                        0%,
                        40% {
                            -webkit-animation-timing-function: ease-in;
                            animation-timing-function: ease-in;
                        }
                        40% {
                            -webkit-transform: perspective(400px) rotateY(-20deg);
                            transform: perspective(400px) rotateY(-20deg);
                        }
                        60% {
                            -webkit-transform: perspective(400px) rotateY(10deg);
                            transform: perspective(400px) rotateY(10deg);
                            opacity: 1;
                        }
                        80% {
                            -webkit-transform: perspective(400px) rotateY(-5deg);
                            transform: perspective(400px) rotateY(-5deg);
                        }
                        to {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                        }
                        }
                        .flipInY {
                        -webkit-backface-visibility: visible !important;
                        backface-visibility: visible !important;
                        -webkit-animation-name: flipInY;
                        animation-name: flipInY;
                        }
                        @-webkit-keyframes flipOutX {
                        0% {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                        }
                        30% {
                            -webkit-transform: perspective(400px) rotateX(-20deg);
                            transform: perspective(400px) rotateX(-20deg);
                            opacity: 1;
                        }
                        to {
                            -webkit-transform: perspective(400px) rotateX(90deg);
                            transform: perspective(400px) rotateX(90deg);
                            opacity: 0;
                        }
                        }
                        @keyframes flipOutX {
                        0% {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                        }
                        30% {
                            -webkit-transform: perspective(400px) rotateX(-20deg);
                            transform: perspective(400px) rotateX(-20deg);
                            opacity: 1;
                        }
                        to {
                            -webkit-transform: perspective(400px) rotateX(90deg);
                            transform: perspective(400px) rotateX(90deg);
                            opacity: 0;
                        }
                        }
                        .flipOutX {
                        -webkit-animation-name: flipOutX;
                        animation-name: flipOutX;
                        -webkit-backface-visibility: visible !important;
                        backface-visibility: visible !important;
                        }
                        @-webkit-keyframes flipOutY {
                        0% {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                        }
                        30% {
                            -webkit-transform: perspective(400px) rotateY(-15deg);
                            transform: perspective(400px) rotateY(-15deg);
                            opacity: 1;
                        }
                        to {
                            -webkit-transform: perspective(400px) rotateY(90deg);
                            transform: perspective(400px) rotateY(90deg);
                            opacity: 0;
                        }
                        }
                        @keyframes flipOutY {
                        0% {
                            -webkit-transform: perspective(400px);
                            transform: perspective(400px);
                        }
                        30% {
                            -webkit-transform: perspective(400px) rotateY(-15deg);
                            transform: perspective(400px) rotateY(-15deg);
                            opacity: 1;
                        }
                        to {
                            -webkit-transform: perspective(400px) rotateY(90deg);
                            transform: perspective(400px) rotateY(90deg);
                            opacity: 0;
                        }
                        }
                        .flipOutY {
                        -webkit-backface-visibility: visible !important;
                        backface-visibility: visible !important;
                        -webkit-animation-name: flipOutY;
                        animation-name: flipOutY;
                        }
                        @-webkit-keyframes lightSpeedIn {
                        0% {
                            -webkit-transform: translate3d(100%, 0, 0) skewX(-30deg);
                            transform: translate3d(100%, 0, 0) skewX(-30deg);
                            opacity: 0;
                        }
                        60% {
                            -webkit-transform: skewX(20deg);
                            transform: skewX(20deg);
                        }
                        60%,
                        80% {
                            opacity: 1;
                        }
                        80% {
                            -webkit-transform: skewX(-5deg);
                            transform: skewX(-5deg);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        @keyframes lightSpeedIn {
                        0% {
                            -webkit-transform: translate3d(100%, 0, 0) skewX(-30deg);
                            transform: translate3d(100%, 0, 0) skewX(-30deg);
                            opacity: 0;
                        }
                        60% {
                            -webkit-transform: skewX(20deg);
                            transform: skewX(20deg);
                        }
                        60%,
                        80% {
                            opacity: 1;
                        }
                        80% {
                            -webkit-transform: skewX(-5deg);
                            transform: skewX(-5deg);
                        }
                        to {
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        .lightSpeedIn {
                        -webkit-animation-name: lightSpeedIn;
                        animation-name: lightSpeedIn;
                        -webkit-animation-timing-function: ease-out;
                        animation-timing-function: ease-out;
                        }
                        @-webkit-keyframes lightSpeedOut {
                        0% {
                            opacity: 1;
                        }
                        to {
                            -webkit-transform: translate3d(100%, 0, 0) skewX(30deg);
                            transform: translate3d(100%, 0, 0) skewX(30deg);
                            opacity: 0;
                        }
                        }
                        @keyframes lightSpeedOut {
                        0% {
                            opacity: 1;
                        }
                        to {
                            -webkit-transform: translate3d(100%, 0, 0) skewX(30deg);
                            transform: translate3d(100%, 0, 0) skewX(30deg);
                            opacity: 0;
                        }
                        }
                        .lightSpeedOut {
                        -webkit-animation-name: lightSpeedOut;
                        animation-name: lightSpeedOut;
                        -webkit-animation-timing-function: ease-in;
                        animation-timing-function: ease-in;
                        }
                        @-webkit-keyframes rotateIn {
                        0% {
                            transform-origin: center;
                            -webkit-transform: rotate(-200deg);
                            transform: rotate(-200deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: center;
                        }
                        to {
                            transform-origin: center;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        @keyframes rotateIn {
                        0% {
                            transform-origin: center;
                            -webkit-transform: rotate(-200deg);
                            transform: rotate(-200deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: center;
                        }
                        to {
                            transform-origin: center;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        .rotateIn {
                        -webkit-animation-name: rotateIn;
                        animation-name: rotateIn;
                        }
                        @-webkit-keyframes rotateInDownLeft {
                        0% {
                            transform-origin: left bottom;
                            -webkit-transform: rotate(-45deg);
                            transform: rotate(-45deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: left bottom;
                        }
                        to {
                            transform-origin: left bottom;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        @keyframes rotateInDownLeft {
                        0% {
                            transform-origin: left bottom;
                            -webkit-transform: rotate(-45deg);
                            transform: rotate(-45deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: left bottom;
                        }
                        to {
                            transform-origin: left bottom;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        .rotateInDownLeft {
                        -webkit-animation-name: rotateInDownLeft;
                        animation-name: rotateInDownLeft;
                        }
                        @-webkit-keyframes rotateInDownRight {
                        0% {
                            transform-origin: right bottom;
                            -webkit-transform: rotate(45deg);
                            transform: rotate(45deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: right bottom;
                        }
                        to {
                            transform-origin: right bottom;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        @keyframes rotateInDownRight {
                        0% {
                            transform-origin: right bottom;
                            -webkit-transform: rotate(45deg);
                            transform: rotate(45deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: right bottom;
                        }
                        to {
                            transform-origin: right bottom;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        .rotateInDownRight {
                        -webkit-animation-name: rotateInDownRight;
                        animation-name: rotateInDownRight;
                        }
                        @-webkit-keyframes rotateInUpLeft {
                        0% {
                            transform-origin: left bottom;
                            -webkit-transform: rotate(45deg);
                            transform: rotate(45deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: left bottom;
                        }
                        to {
                            transform-origin: left bottom;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        @keyframes rotateInUpLeft {
                        0% {
                            transform-origin: left bottom;
                            -webkit-transform: rotate(45deg);
                            transform: rotate(45deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: left bottom;
                        }
                        to {
                            transform-origin: left bottom;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        .rotateInUpLeft {
                        -webkit-animation-name: rotateInUpLeft;
                        animation-name: rotateInUpLeft;
                        }
                        @-webkit-keyframes rotateInUpRight {
                        0% {
                            transform-origin: right bottom;
                            -webkit-transform: rotate(-90deg);
                            transform: rotate(-90deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: right bottom;
                        }
                        to {
                            transform-origin: right bottom;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        @keyframes rotateInUpRight {
                        0% {
                            transform-origin: right bottom;
                            -webkit-transform: rotate(-90deg);
                            transform: rotate(-90deg);
                            opacity: 0;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: right bottom;
                        }
                        to {
                            transform-origin: right bottom;
                            -webkit-transform: none;
                            transform: none;
                            opacity: 1;
                        }
                        }
                        .rotateInUpRight {
                        -webkit-animation-name: rotateInUpRight;
                        animation-name: rotateInUpRight;
                        }
                        @-webkit-keyframes rotateOut {
                        0% {
                            transform-origin: center;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: center;
                        }
                        to {
                            transform-origin: center;
                            -webkit-transform: rotate(200deg);
                            transform: rotate(200deg);
                            opacity: 0;
                        }
                        }
                        @keyframes rotateOut {
                        0% {
                            transform-origin: center;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: center;
                        }
                        to {
                            transform-origin: center;
                            -webkit-transform: rotate(200deg);
                            transform: rotate(200deg);
                            opacity: 0;
                        }
                        }
                        .rotateOut {
                        -webkit-animation-name: rotateOut;
                        animation-name: rotateOut;
                        }
                        @-webkit-keyframes rotateOutDownLeft {
                        0% {
                            transform-origin: left bottom;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: left bottom;
                        }
                        to {
                            transform-origin: left bottom;
                            -webkit-transform: rotate(45deg);
                            transform: rotate(45deg);
                            opacity: 0;
                        }
                        }
                        @keyframes rotateOutDownLeft {
                        0% {
                            transform-origin: left bottom;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: left bottom;
                        }
                        to {
                            transform-origin: left bottom;
                            -webkit-transform: rotate(45deg);
                            transform: rotate(45deg);
                            opacity: 0;
                        }
                        }
                        .rotateOutDownLeft {
                        -webkit-animation-name: rotateOutDownLeft;
                        animation-name: rotateOutDownLeft;
                        }
                        @-webkit-keyframes rotateOutDownRight {
                        0% {
                            transform-origin: right bottom;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: right bottom;
                        }
                        to {
                            transform-origin: right bottom;
                            -webkit-transform: rotate(-45deg);
                            transform: rotate(-45deg);
                            opacity: 0;
                        }
                        }
                        @keyframes rotateOutDownRight {
                        0% {
                            transform-origin: right bottom;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: right bottom;
                        }
                        to {
                            transform-origin: right bottom;
                            -webkit-transform: rotate(-45deg);
                            transform: rotate(-45deg);
                            opacity: 0;
                        }
                        }
                        .rotateOutDownRight {
                        -webkit-animation-name: rotateOutDownRight;
                        animation-name: rotateOutDownRight;
                        }
                        @-webkit-keyframes rotateOutUpLeft {
                        0% {
                            transform-origin: left bottom;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: left bottom;
                        }
                        to {
                            transform-origin: left bottom;
                            -webkit-transform: rotate(-45deg);
                            transform: rotate(-45deg);
                            opacity: 0;
                        }
                        }
                        @keyframes rotateOutUpLeft {
                        0% {
                            transform-origin: left bottom;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: left bottom;
                        }
                        to {
                            transform-origin: left bottom;
                            -webkit-transform: rotate(-45deg);
                            transform: rotate(-45deg);
                            opacity: 0;
                        }
                        }
                        .rotateOutUpLeft {
                        -webkit-animation-name: rotateOutUpLeft;
                        animation-name: rotateOutUpLeft;
                        }
                        @-webkit-keyframes rotateOutUpRight {
                        0% {
                            transform-origin: right bottom;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: right bottom;
                        }
                        to {
                            transform-origin: right bottom;
                            -webkit-transform: rotate(90deg);
                            transform: rotate(90deg);
                            opacity: 0;
                        }
                        }
                        @keyframes rotateOutUpRight {
                        0% {
                            transform-origin: right bottom;
                            opacity: 1;
                        }
                        0%,
                        to {
                            -webkit-transform-origin: right bottom;
                        }
                        to {
                            transform-origin: right bottom;
                            -webkit-transform: rotate(90deg);
                            transform: rotate(90deg);
                            opacity: 0;
                        }
                        }
                        .rotateOutUpRight {
                        -webkit-animation-name: rotateOutUpRight;
                        animation-name: rotateOutUpRight;
                        }
                        @-webkit-keyframes hinge {
                        0% {
                            transform-origin: top left;
                        }
                        0%,
                        20%,
                        60% {
                            -webkit-transform-origin: top left;
                            -webkit-animation-timing-function: ease-in-out;
                            animation-timing-function: ease-in-out;
                        }
                        20%,
                        60% {
                            -webkit-transform: rotate(80deg);
                            transform: rotate(80deg);
                            transform-origin: top left;
                        }
                        40%,
                        80% {
                            -webkit-transform: rotate(60deg);
                            transform: rotate(60deg);
                            -webkit-transform-origin: top left;
                            transform-origin: top left;
                            -webkit-animation-timing-function: ease-in-out;
                            animation-timing-function: ease-in-out;
                            opacity: 1;
                        }
                        to {
                            -webkit-transform: translate3d(0, 700px, 0);
                            transform: translate3d(0, 700px, 0);
                            opacity: 0;
                        }
                        }
                        @keyframes hinge {
                        0% {
                            transform-origin: top left;
                        }
                        0%,
                        20%,
                        60% {
                            -webkit-transform-origin: top left;
                            -webkit-animation-timing-function: ease-in-out;
                            animation-timing-function: ease-in-out;
                        }
                        20%,
                        60% {
                            -webkit-transform: rotate(80deg);
                            transform: rotate(80deg);
                            transform-origin: top left;
                        }
                        40%,
                        80% {
                            -webkit-transform: rotate(60deg);
                            transform: rotate(60deg);
                            -webkit-transform-origin: top left;
                            transform-origin: top left;
                            -webkit-animation-timing-function: ease-in-out;
                            animation-timing-function: ease-in-out;
                            opacity: 1;
                        }
                        to {
                            -webkit-transform: translate3d(0, 700px, 0);
                            transform: translate3d(0, 700px, 0);
                            opacity: 0;
                        }
                        }
                        .hinge {
                        -webkit-animation-name: hinge;
                        animation-name: hinge;
                        }
                        @-webkit-keyframes rollIn {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(-100%, 0, 0) rotate(-120deg);
                            transform: translate3d(-100%, 0, 0) rotate(-120deg);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        @keyframes rollIn {
                        0% {
                            opacity: 0;
                            -webkit-transform: translate3d(-100%, 0, 0) rotate(-120deg);
                            transform: translate3d(-100%, 0, 0) rotate(-120deg);
                        }
                        to {
                            opacity: 1;
                            -webkit-transform: none;
                            transform: none;
                        }
                        }
                        .rollIn {
                        -webkit-animation-name: rollIn;
                        animation-name: rollIn;
                        }
                        @-webkit-keyframes rollOut {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(100%, 0, 0) rotate(120deg);
                            transform: translate3d(100%, 0, 0) rotate(120deg);
                        }
                        }
                        @keyframes rollOut {
                        0% {
                            opacity: 1;
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: translate3d(100%, 0, 0) rotate(120deg);
                            transform: translate3d(100%, 0, 0) rotate(120deg);
                        }
                        }
                        .rollOut {
                        -webkit-animation-name: rollOut;
                        animation-name: rollOut;
                        }
                        @-webkit-keyframes zoomIn {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.3, 0.3, 0.3);
                            transform: scale3d(0.3, 0.3, 0.3);
                        }
                        50% {
                            opacity: 1;
                        }
                        }
                        @keyframes zoomIn {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.3, 0.3, 0.3);
                            transform: scale3d(0.3, 0.3, 0.3);
                        }
                        50% {
                            opacity: 1;
                        }
                        }
                        .zoomIn {
                        -webkit-animation-name: zoomIn;
                        animation-name: zoomIn;
                        }
                        @-webkit-keyframes zoomInDown {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(0, -1000px, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(0, -1000px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(0, 60px, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(0, 60px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        @keyframes zoomInDown {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(0, -1000px, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(0, -1000px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(0, 60px, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(0, 60px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        .zoomInDown {
                        -webkit-animation-name: zoomInDown;
                        animation-name: zoomInDown;
                        }
                        @-webkit-keyframes zoomInLeft {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(-1000px, 0, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(-1000px, 0, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(10px, 0, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(10px, 0, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        @keyframes zoomInLeft {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(-1000px, 0, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(-1000px, 0, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(10px, 0, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(10px, 0, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        .zoomInLeft {
                        -webkit-animation-name: zoomInLeft;
                        animation-name: zoomInLeft;
                        }
                        @-webkit-keyframes zoomInRight {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(1000px, 0, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(1000px, 0, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(-10px, 0, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(-10px, 0, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        @keyframes zoomInRight {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(1000px, 0, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(1000px, 0, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(-10px, 0, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(-10px, 0, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        .zoomInRight {
                        -webkit-animation-name: zoomInRight;
                        animation-name: zoomInRight;
                        }
                        @-webkit-keyframes zoomInUp {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(0, 1000px, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(0, 1000px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(0, -60px, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(0, -60px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        @keyframes zoomInUp {
                        0% {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(0, 1000px, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(0, 1000px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        60% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(0, -60px, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(0, -60px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        .zoomInUp {
                        -webkit-animation-name: zoomInUp;
                        animation-name: zoomInUp;
                        }
                        @-webkit-keyframes zoomOut {
                        0% {
                            opacity: 1;
                        }
                        50% {
                            -webkit-transform: scale3d(0.3, 0.3, 0.3);
                            transform: scale3d(0.3, 0.3, 0.3);
                        }
                        50%,
                        to {
                            opacity: 0;
                        }
                        }
                        @keyframes zoomOut {
                        0% {
                            opacity: 1;
                        }
                        50% {
                            -webkit-transform: scale3d(0.3, 0.3, 0.3);
                            transform: scale3d(0.3, 0.3, 0.3);
                        }
                        50%,
                        to {
                            opacity: 0;
                        }
                        }
                        .zoomOut {
                        -webkit-animation-name: zoomOut;
                        animation-name: zoomOut;
                        }
                        @-webkit-keyframes zoomOutDown {
                        40% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(0, -60px, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(0, -60px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(0, 2000px, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(0, 2000px, 0);
                            -webkit-transform-origin: center bottom;
                            transform-origin: center bottom;
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        @keyframes zoomOutDown {
                        40% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(0, -60px, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(0, -60px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(0, 2000px, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(0, 2000px, 0);
                            -webkit-transform-origin: center bottom;
                            transform-origin: center bottom;
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        .zoomOutDown {
                        -webkit-animation-name: zoomOutDown;
                        animation-name: zoomOutDown;
                        }
                        @-webkit-keyframes zoomOutLeft {
                        40% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(42px, 0, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(42px, 0, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale(0.1) translate3d(-2000px, 0, 0);
                            transform: scale(0.1) translate3d(-2000px, 0, 0);
                            -webkit-transform-origin: left center;
                            transform-origin: left center;
                        }
                        }
                        @keyframes zoomOutLeft {
                        40% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(42px, 0, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(42px, 0, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale(0.1) translate3d(-2000px, 0, 0);
                            transform: scale(0.1) translate3d(-2000px, 0, 0);
                            -webkit-transform-origin: left center;
                            transform-origin: left center;
                        }
                        }
                        .zoomOutLeft {
                        -webkit-animation-name: zoomOutLeft;
                        animation-name: zoomOutLeft;
                        }
                        @-webkit-keyframes zoomOutRight {
                        40% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(-42px, 0, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(-42px, 0, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale(0.1) translate3d(2000px, 0, 0);
                            transform: scale(0.1) translate3d(2000px, 0, 0);
                            -webkit-transform-origin: right center;
                            transform-origin: right center;
                        }
                        }
                        @keyframes zoomOutRight {
                        40% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(-42px, 0, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(-42px, 0, 0);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale(0.1) translate3d(2000px, 0, 0);
                            transform: scale(0.1) translate3d(2000px, 0, 0);
                            -webkit-transform-origin: right center;
                            transform-origin: right center;
                        }
                        }
                        .zoomOutRight {
                        -webkit-animation-name: zoomOutRight;
                        animation-name: zoomOutRight;
                        }
                        @-webkit-keyframes zoomOutUp {
                        40% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(0, 60px, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(0, 60px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(0, -2000px, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(0, -2000px, 0);
                            -webkit-transform-origin: center bottom;
                            transform-origin: center bottom;
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        @keyframes zoomOutUp {
                        40% {
                            opacity: 1;
                            -webkit-transform: scale3d(0.475, 0.475, 0.475) translate3d(0, 60px, 0);
                            transform: scale3d(0.475, 0.475, 0.475) translate3d(0, 60px, 0);
                            -webkit-animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
                        }
                        to {
                            opacity: 0;
                            -webkit-transform: scale3d(0.1, 0.1, 0.1) translate3d(0, -2000px, 0);
                            transform: scale3d(0.1, 0.1, 0.1) translate3d(0, -2000px, 0);
                            -webkit-transform-origin: center bottom;
                            transform-origin: center bottom;
                            -webkit-animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                            animation-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1);
                        }
                        }
                        .zoomOutUp {
                        -webkit-animation-name: zoomOutUp;
                        animation-name: zoomOutUp;
                        }
                        @-webkit-keyframes slideInDown {
                        0% {
                            -webkit-transform: translate3d(0, -100%, 0);
                            transform: translate3d(0, -100%, 0);
                            visibility: visible;
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        @keyframes slideInDown {
                        0% {
                            -webkit-transform: translate3d(0, -100%, 0);
                            transform: translate3d(0, -100%, 0);
                            visibility: visible;
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        .slideInDown {
                        -webkit-animation-name: slideInDown;
                        animation-name: slideInDown;
                        }
                        @-webkit-keyframes slideInLeft {
                        0% {
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                            visibility: visible;
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        @keyframes slideInLeft {
                        0% {
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                            visibility: visible;
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        .slideInLeft {
                        -webkit-animation-name: slideInLeft;
                        animation-name: slideInLeft;
                        }
                        @-webkit-keyframes slideInRight {
                        0% {
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                            visibility: visible;
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        @keyframes slideInRight {
                        0% {
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                            visibility: visible;
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        .slideInRight {
                        -webkit-animation-name: slideInRight;
                        animation-name: slideInRight;
                        }
                        @-webkit-keyframes slideInUp {
                        0% {
                            -webkit-transform: translate3d(0, 100%, 0);
                            transform: translate3d(0, 100%, 0);
                            visibility: visible;
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        @keyframes slideInUp {
                        0% {
                            -webkit-transform: translate3d(0, 100%, 0);
                            transform: translate3d(0, 100%, 0);
                            visibility: visible;
                        }
                        to {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        }
                        .slideInUp {
                        -webkit-animation-name: slideInUp;
                        animation-name: slideInUp;
                        }
                        @-webkit-keyframes slideOutDown {
                        0% {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        to {
                            visibility: hidden;
                            -webkit-transform: translate3d(0, 100%, 0);
                            transform: translate3d(0, 100%, 0);
                        }
                        }
                        @keyframes slideOutDown {
                        0% {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        to {
                            visibility: hidden;
                            -webkit-transform: translate3d(0, 100%, 0);
                            transform: translate3d(0, 100%, 0);
                        }
                        }
                        .slideOutDown {
                        -webkit-animation-name: slideOutDown;
                        animation-name: slideOutDown;
                        }
                        @-webkit-keyframes slideOutLeft {
                        0% {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        to {
                            visibility: hidden;
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                        }
                        }
                        @keyframes slideOutLeft {
                        0% {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        to {
                            visibility: hidden;
                            -webkit-transform: translate3d(-100%, 0, 0);
                            transform: translate3d(-100%, 0, 0);
                        }
                        }
                        .slideOutLeft {
                        -webkit-animation-name: slideOutLeft;
                        animation-name: slideOutLeft;
                        }
                        @-webkit-keyframes slideOutRight {
                        0% {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        to {
                            visibility: hidden;
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                        }
                        }
                        @keyframes slideOutRight {
                        0% {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        to {
                            visibility: hidden;
                            -webkit-transform: translate3d(100%, 0, 0);
                            transform: translate3d(100%, 0, 0);
                        }
                        }
                        .slideOutRight {
                        -webkit-animation-name: slideOutRight;
                        animation-name: slideOutRight;
                        }
                        @-webkit-keyframes slideOutUp {
                        0% {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        to {
                            visibility: hidden;
                            -webkit-transform: translate3d(0, -100%, 0);
                            transform: translate3d(0, -100%, 0);
                        }
                        }
                        @keyframes slideOutUp {
                        0% {
                            -webkit-transform: translateZ(0);
                            transform: translateZ(0);
                        }
                        to {
                            visibility: hidden;
                            -webkit-transform: translate3d(0, -100%, 0);
                            transform: translate3d(0, -100%, 0);
                        }
                        }
                        .slideOutUp {
                        -webkit-animation-name: slideOutUp;
                        animation-name: slideOutUp;
                        }
                        @font-face {
                        font-family: SolaimanLipi;
                        src: url(../fonts/SolaimanLipi/SolaimanLipi_29-05-06.eot?) format("eot"),
                            url(../fonts/SolaimanLipi/SolaimanLipi_29-05-06.woff) format("woff"),
                            url(../fonts/SolaimanLipi/SolaimanLipi_29-05-06.ttf) format("truetype"),
                            url(../fonts/SolaimanLipi/SolaimanLipi_29-05-06.svg#SolaimanLipi)
                            format("svg");
                        font-weight: 400;
                        font-style: normal;
                        }
                        @font-face {
                        font-family: boishkhi;
                        src: url(../fonts/Boishkhi/Boishkhi.eot);
                        src: url(../fonts/Boishkhi/Boishkhi.woff) format("woff"),
                            url(../fonts/Boishkhi/Boishkhi.ttf) format("truetype"),
                            url(../fonts/Boishkhi/Boishkhi.svg) format("svg");
                        font-weight: 400;
                        font-style: normal;
                        }
                        @font-face {
                        font-family: kalpurush;
                        src: url(../fonts/Kalpurush/kalpurush.eot);
                        src: url(../fonts/Kalpurush/kalpurush.woff) format("woff"),
                            url(../fonts/Kalpurush/kalpurush.ttf) format("truetype"),
                            url(../fonts/Kalpurush/kalpurush.svg) format("svg");
                        font-weight: 400;
                        font-style: normal;
                        }
                        body {
                        font-family: kalpurush, sans-serif !important;
                        }
                        body,
                        html {
                        font-size: 15px;
                        font-family: kalpurush, sans-serif !important;
                        overflow-x: hidden;
                        }

                        .main-wrapper {
                        width: 80%;
                        max-width: 1366px;
                        background: rgba(255, 255, 255, 0.9);
                        box-shadow: 0 1px 8px 1px rgba(85, 85, 85, 0.5);
                        }
                        .main-wrapper .btn {
                        border-radius: 0;
                        }
                        .main-wrapper .form-control {
                        padding: 6px;
                        border-radius: 0;
                        border: 1px solid #ddd;
                        -webkit-box-shadow: none;
                        box-shadow: none;
                        }
                        .main-wrapper .form-control:hover {
                        border-color: rgba(158, 91, 186, 0.64);
                        }
                        .copyInfo_wrapper {
                        width: 20px;
                        height: 20px;
                        margin-left: 10px;
                        display: inline-block;
                        }
                        .main-wrapper #copyInfo.form-control {
                        height: 20px;
                        }
                        .full-width,
                        .width100p {
                        width: 100%;
                        }
                        .width23px {
                        width: 23px;
                        }
                        .width28px {
                        width: 28px;
                        }
                        .width25px {
                        width: 25px;
                        }
                        .width20p {
                        width: 20%;
                        }
                        .width25p {
                        width: 25%;
                        }
                        .width27p {
                        width: 27%;
                        }
                        .width30p {
                        width: 30%;
                        }
                        .width35p {
                        width: 35%;
                        }
                        .width40p {
                        width: 40%;
                        }
                        .width45p {
                        width: 45%;
                        }
                        .width50p {
                        width: 50%;
                        }
                        .width55p {
                        width: 55%;
                        }
                        .width_67p {
                        width: 67%;
                        }
                        .width72p {
                        width: 72%;
                        }
                        .width_190 {
                        width: 190px;
                        }
                        .position-relative {
                        position: relative;
                        }
                        .position-absolute {
                        position: absolute;
                        }
                        .position-fixed {
                        position: fixed;
                        }
                        .ellipsis-3 {
                        display: block;
                        display: -webkit-box;
                        height: 65px;
                        margin: 0 auto;
                        line-height: 19px;
                        -webkit-line-clamp: 3;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        }
                        a {
                        color: #555;
                        cursor: pointer;
                        line-height: 20px;
                        text-decoration: none;
                        -webkit-transition: all 0.5s linear;
                        -moz-transition: all 0.5s linear;
                        -o-transition: all 0.5s linear;
                        transition: all 0.5s linear;
                        }
                        a.dashboard {
                        color: #fff;
                        }
                        a:focus,
                        a:hover {
                        color: #34a400;
                        text-decoration: underline;
                        -webkit-transition: all 0.5s linear;
                        -moz-transition: all 0.5s linear;
                        -o-transition: all 0.5s linear;
                        transition: all 0.5s linear;
                        }
                        h3.page-heading {
                        color: #fff;
                        padding: 0;
                        margin: 0 0 15px;
                        font-size: 18px;
                        border-bottom: 2px solid #8cc643;
                        }
                        h3.page-heading > span {
                        padding: 7px 15px 3px;
                        background: #92bb38;
                        display: inline-block;
                        line-height: 24px;
                        }
                        .panel-title {
                        font-size: 17px;
                        }
                        #FAQ_CONTAINER .panel-title {
                        font-size: 15px;
                        color: #55565a;
                        }
                        .section-title {
                        color: #48b322;
                        margin: 0 0 15px 0;
                        padding-bottom: 5px;
                        border-bottom: 1px solid #ddd;
                        }
                        .color-violet-light {
                        color: #9e5bba;
                        }
                        .color-violet-deep {
                        color: #683091;
                        }
                        .bg-gray {
                        background: #eee;
                        }
                        .bg-violet-light {
                        background: #9e5bba;
                        }
                        .bg-violet-deep {
                        background: #683091;
                        }
                        .verticle-align-top {
                        vertical-align: top;
                        }
                        .main-wrapper label {
                        font-weight: 500;
                        }
                        .profile-wrapper .form-horizontal .control-label {
                        width: 145px;
                        padding-top: 2px;
                        text-align: left;
                        line-height: 16px;
                        }
                        .profile-wrapper .form-horizontal .form-group {
                        padding-bottom: 6px;
                        margin-bottom: 6px;
                        border-bottom: 1px dashed #e0e0e0;
                        }
                        .profile-wrapper .nav-tabs > li > a {
                        background-color: #eee;
                        }
                        .profile-wrapper .nav-tabs > li.active > a {
                        background-color: #fff;
                        }
                        .main-wrapper .profile-wrapper .tab-content {
                        border-top: none;
                        }
                        .custom-panel.panel-primary {
                        border-radius: 0;
                        margin-bottom: 10px;
                        border: 1px solid #cbced2;
                        -webkit-box-shadow: 0 1px 5px #ccc;
                        -moz-box-shadow: 0 1px 5px #ccc;
                        box-shadow: 0 1px 5px #ccc;
                        }
                        .custom-panel.panel-primary > .panel-heading {
                        border-radius: 0;
                        background-color: #48b322;
                        border-color: #48b322;
                        }
                        .custom-panel .panel-body {
                        padding: 10px;
                        border: 1px solid transparent;
                        border-top: 0;
                        }
                        .custom-panel.panel-primary > .panel-heading.bg-violet-deep {
                        color: #555;
                        border-radius: 0;
                        background-color: #e4e6eced;
                        border-color: #c9cdd6;
                        }
                        .custom-panel .panel-body.padding0 {
                        padding: 0;
                        }
                        .main-wrapper .form-control:focus {
                        border-color: rgba(158, 91, 186, 0.64);
                        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
                            0 0 8px rgba(158, 91, 186, 0.4);
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
                            0 0 8px rgba(158, 91, 186, 0.4);
                        }
                        .font-family-default {
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                        }
                        .font-family-boishkhi {
                        font-family: boishkhi;
                        }
                        .inline-block {
                        display: inline-block;
                        }
                        .font-size12 {
                        font-size: 12px;
                        }
                        .font-size13 {
                        font-size: 13px;
                        }
                        .font-size14 {
                        font-size: 14px;
                        }
                        .font-size15 {
                        font-size: 15px;
                        }
                        .font-size16 {
                        font-size: 16px;
                        }
                        .font-size22 {
                        font-size: 22px;
                        }
                        .font-size30 {
                        font-size: 30px;
                        }
                        .font-bold {
                        font-weight: 700;
                        }
                        .font-normal {
                        font-weight: 400;
                        }
                        .padding0 {
                        padding: 0;
                        }
                        .padding0_imp {
                        padding: 0 !important;
                        }
                        .padding5 {
                        padding: 5px;
                        }
                        .padding-lr0 {
                        padding-left: 0;
                        padding-right: 0;
                        }
                        .padding-lr15 {
                        padding-left: 15px;
                        padding-right: 15px;
                        }
                        .padding-l0 {
                        padding-left: 0;
                        }
                        .padding-l18 {
                        padding-left: 18px;
                        }
                        .padding-left36 {
                        padding-left: 36px;
                        }
                        .padding-r0 {
                        padding-right: 0;
                        }
                        .padding-r15 {
                        padding-right: 15px;
                        }
                        .padding-tb15 {
                        padding: 15px 0;
                        }
                        .padding-bottom0 {
                        padding-bottom: 0;
                        }
                        .margin0 {
                        margin: 0;
                        }
                        .margin-top0 {
                        margin-top: 0;
                        }
                        .margin-lr15 {
                        margin-left: 15px;
                        margin-right: 15px;
                        }
                        .margin-top5 {
                        margin-top: 5px;
                        }
                        .margin-top10 {
                        margin-top: 10px;
                        }
                        .margin-top20 {
                        margin-top: 20px;
                        }
                        .margin-top25 {
                        margin-top: 25px;
                        }
                        .margin-top30 {
                        margin-top: 30px;
                        }
                        .margin-top-37 {
                        margin-top: 37px;
                        }
                        .margin-top-45 {
                        margin-top: 45px;
                        }
                        .margin-top-50 {
                        margin-top: 50px;
                        }
                        .margin-top-60 {
                        margin-top: 60px;
                        }
                        .margin-top-100 {
                        margin-top: 100px;
                        }
                        .margin-top-150 {
                        margin-top: 150px;
                        }
                        .margin-tb-10 {
                        margin-top: 10px;
                        margin-bottom: 10px;
                        }
                        .margin-tb-30 {
                        margin-top: 30px;
                        margin-bottom: 30px;
                        }
                        .margin-bottom-0 {
                        margin-bottom: 0;
                        }
                        .margin-bottom-5 {
                        margin-bottom: 5px;
                        }
                        .margin-bottom-10 {
                        margin-bottom: 10px;
                        }
                        .margin-bottom-20 {
                        margin-bottom: 20px;
                        }
                        .margin-bottom-30 {
                        margin-bottom: 30px;
                        }
                        .margin-bottom-40 {
                        margin-bottom: 40px;
                        }
                        .margin-left10 {
                        margin-left: 10px;
                        }
                        .margin-left15 {
                        margin-left: 15px;
                        }
                        .margin-r0 {
                        margin-right: 0;
                        }
                        .margin-r10 {
                        margin-right: 10px;
                        }
                        .margin-r20 {
                        margin-right: 20px;
                        }
                        .margin-r30 {
                        margin-right: 30px;
                        }
                        .line-height24 {
                        line-height: 24px;
                        }
                        .height_140px {
                        height: 140px;
                        }
                        .line-height35 {
                        line-height: 35px;
                        }
                        .list-style-none {
                        list-style: none;
                        }
                        .red {
                        color: #e40000;
                        }
                        .light-green {
                        color: #92bb38;
                        }
                        .ellipsis-2,
                        .ellipsis-3,
                        .ellipsis-4 {
                        display: -webkit-box;
                        white-space: normal;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        -webkit-box-orient: vertical;
                        word-wrap: break-word;
                        }
                        .ellipsis-2 {
                        -webkit-line-clamp: 2;
                        }
                        .ellipsis-3 {
                        -webkit-line-clamp: 3;
                        }
                        .ellipsis-4 {
                        -webkit-line-clamp: 4;
                        }
                        #mark {
                        color: #e40000;
                        }
                        .top_title {
                        line-height: 18px;
                        display: block;
                        clear: right;
                        font-size: 15px;
                        }
                        .top_sub_title {
                        line-height: 16px;
                        display: block;
                        font-size: 12px;
                        }
                        #addChalanForm .form-group {
                        margin-bottom: 8px;
                        padding-bottom: 15px;
                        max-height: 74px;
                        position: relative;
                        }
                        #addChalanForm #multiChallanSection .table > thead > tr > th {
                        padding: 8px 5px;
                        border: 1px solid #d2cdcd;
                        background: #ddd;
                        }
                        #addChalanForm #multiChallanSection .table-bordered > tbody > tr > td {
                        border: 1px solid #d2cdcd;
                        }
                        #addChalanForm #multiple-data-container.table > tbody > tr > td {
                        padding: 5px;
                        }
                        #addChalanForm #multiple-data-container .form-control {
                        padding: 6px 2px;
                        }
                        #addChalanForm #multiple-data-container select.form-control {
                        padding: 6px 0;
                        }
                        #addChalanForm #multiple-data-container .form-group {
                        margin-bottom: 0;
                        padding-bottom: 0;
                        padding-right: 5px;
                        padding-left: 0;
                        }
                        #addChalanForm #multiple-data-container .form-group.padding-r0 {
                        padding-right: 0;
                        }
                        .error,
                        .error-msg,
                        .form-group label.error {
                        color: #ea3c3b;
                        font-size: 12px;
                        font-weight: 100;
                        line-height: 16px;
                        }
                        .registration-page-wrapper .form-group {
                        position: relative;
                        }
                        .registration-page-wrapper label.error {
                        position: absolute;
                        right: 15px;
                        top: 5px;
                        }
                        .registration-page-wrapper label[for="captcha"].error {
                        position: absolute;
                        left: 75px;
                        top: 5px;
                        }
                        #addChalanForm .form-group label.error {
                        line-height: 14px;
                        margin-bottom: 0;
                        position: absolute;
                        left: 15px;
                        bottom: -3px;
                        }
                        #addChalanForm .form-group .terms-wrapper label.error {
                        position: static;
                        }
                        .form-group .form-control.error,
                        .form-group .form-control.error:focus {
                        border: 1px solid #ea605f;
                        }
                        .form-group .terms-wrapper label.error {
                        left: 210px;
                        }
                        .home-login_wrapper .form-group {
                        position: relative;
                        }
                        .home-login_wrapper .form-group label.error {
                        left: 0;
                        bottom: -23px;
                        position: absolute;
                        }
                        .login-wrapper {
                        padding: 30px;
                        margin-top: 30px;
                        padding-bottom: 15px;
                        background-color: #fff;
                        border: 1px solid #ddd;
                        -webkit-box-shadow: 0 3px 12px rgba(0, 0, 0, 0.175);
                        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.175);
                        }
                        .display-block {
                        display: block;
                        }
                        .display-inline-block {
                        display: inline-block;
                        }
                        .btn-default.custom-btn {
                        color: #fff;
                        outline: 0;
                        font-size: 15px;
                        min-width: 95px;
                        background-color: #61319b;
                        border-color: #61319b;
                        -webkit-transition: all 0.2s linear;
                        -moz-transition: all 0.2s linear;
                        -o-transition: all 0.2s linear;
                        transition: all 0.2s linear;
                        }
                        .btn-default.custom-btn.custom-btn-medium {
                        padding: 8px 15px;
                        }
                        .daily_challan_generate_section #challan_generate,
                        .search_page_wrapper input#search {
                        height: 34px;
                        width: 70px;
                        text-align: center;
                        padding: 5px 10px;
                        }
                        .daily_challan_generate_section #challan_generate {
                        width: auto;
                        }
                        .search_page_wrapper #TXN_LIST_GRID td {
                        vertical-align: middle;
                        }
                        #login_error {
                        padding: 5px;
                        height: 34px;
                        background: #e95a59;
                        }
                        #login_error .error-msg {
                        color: #fff;
                        font-size: 13px;
                        line-height: 24px;
                        }
                        .header-wrapper .search-cart-wrap {
                        width: 239px;
                        float: right;
                        margin-top: 8px;
                        }
                        .header-wrapper .search-cart-wrap .advance-search {
                        border: 1px solid #89b22f99;
                        }
                        .header-wrapper .search-cart-wrap .advance-search .sv_search_product {
                        -moz-appearance: none;
                        -webkit-appearance: none;
                        appearance: none;
                        border: none;
                        width: 100px;
                        height: 35px;
                        font-size: 14px;
                        padding: 0 10px;
                        color: #8e8e8e;
                        cursor: pointer;
                        outline: 0;
                        box-shadow: none;
                        background: url(../img/arrow.png) no-repeat;
                        background-size: 10px;
                        background-position: 96% 53%;
                        }
                        .header-wrapper .search-cart-wrap .advance-search .sv_search_form {
                        position: relative;
                        }
                        .header-wrapper .form-control {
                        padding: 6px;
                        border-radius: 0;
                        border: 1px solid #ddd;
                        -webkit-box-shadow: none;
                        box-shadow: none;
                        }
                        .header-wrapper
                        .search-cart-wrap
                        .advance-search
                        .sv_search_form
                        input[type="text"] {
                        width: 100%;
                        height: 32px;
                        padding: 0 45px 0 10px;
                        border-radius: 0;
                        border: 0;
                        outline: 0;
                        color: #8e8e8e;
                        }
                        .header-wrapper
                        .search-cart-wrap
                        .advance-search
                        .sv_search_form
                        input[type="text"]:focus {
                        box-shadow: none;
                        }
                        .header-wrapper
                        .search-cart-wrap
                        .advance-search
                        .sv_search_form
                        #searchsubmit {
                        background-color: #683091;
                        border: medium none;
                        border-radius: 0;
                        box-shadow: none;
                        font-size: 14px;
                        height: 31px;
                        position: absolute;
                        right: 2px;
                        text-shadow: none;
                        top: 2px;
                        width: 38px;
                        transition: all 0.5s ease-in-out 0s;
                        -webkit-transition: all 0.5s ease-in-out 0s;
                        padding: 5px 10px;
                        }
                        .header-wrapper
                        .search-cart-wrap
                        .advance-search
                        .sv_search_form
                        #searchsubmit
                        .fa {
                        color: #fff;
                        }
                        .btn.btn-default.custom-btn.submit-lang {
                        height: 24px;
                        font-size: 13px;
                        margin-top: -2px;
                        padding: 3px 4px 4px;
                        line-height: 16px;
                        background: #9e5bba;
                        border-color: #683091;
                        }
                        .header-top-bar {
                        color: #fff;
                        height: 40px;
                        padding: 4px 10px;
                        line-height: 24px;
                        background-color: #86bc42;
                        border-bottom: 2px solid #bad087;
                        }
                        .header-wrapper {
                        min-height: 80px;
                        }
                        .logo_wrapper {
                        margin-top: 6px;
                        margin-bottom: 6px;
                        }
                        .logo_wrapper img {
                        max-height: 74px;
                        }
                        .logo-title {
                        color: #e40000;
                        font-size: 28px;
                        line-height: 22px;
                        margin: 14px 0 0 0;
                        }
                        .container.navbar-container {
                        width: 100%;
                        }
                        .navbar-container.fixed-theme {
                        max-width: 1080px;
                        }
                        .navbar.navbar-fixed-top .logo-title {
                        color: #8b60c9;
                        font-size: 22px;
                        margin: 16px 0 0 0;
                        }
                        .logo-subtitle {
                        color: #9e5bba;
                        }
                        .captcha-box {
                        max-width: 120px;
                        float: left;
                        margin-right: 10px;
                        max-height: 26px;
                        }
                        .navbar.custom-navbar {
                        min-height: 38px;
                        border-radius: 0;
                        margin-bottom: 10px;
                        }
                        .navbar.custom-navbar .navbar-collapse {
                        padding-left: 0;
                        padding-right: 0;
                        }
                        .top-user-row {
                        height: 36px;
                        }
                        .top-user-row a {
                        color: #eee;
                        }
                        .user-info-wrapper {
                        padding: 0;
                        margin: 0;
                        padding-right: 15px;
                        }
                        .user-info-wrapper li {
                        padding: 10px 0;
                        }
                        .user-info-wrapper li.open {
                        min-width: 170px;
                        }
                        .user-info-wrapper a {
                        font-size: 14px;
                        padding: 5px 13px;
                        color: #787a7f;
                        text-decoration: none;
                        background: #eeeeee70;
                        border: 1px solid #89b22f99;
                        }
                        .user-info-wrapper a:hover {
                        color: #787a7f;
                        background: #89b22f99;
                        }
                        .user-info-wrapper li .caret {
                        margin-right: 6px;
                        }
                        .user-info-wrapper .dropdown-menu {
                        right: 0;
                        padding: 1px;
                        margin: -4px 0 0 0;
                        border-radius: 0;
                        background: #91b455;
                        border: none;
                        }
                        .user-info-wrapper .dropdown-menu li {
                        padding: 0;
                        background: #fff;
                        }
                        .user-info-wrapper .dropdown-menu > li > a {
                        padding: 5px 10px;
                        border: none;
                        background: 0 0;
                        border-top: 1px solid #ddd;
                        }
                        .user-info-wrapper .dropdown-menu li:last-child {
                        border-bottom: none;
                        }
                        .user-info-wrapper .dropdown-menu > li:hover > a {
                        background: #89b22f99;
                        }
                        .user-info-wrapper a .fa {
                        color: #89b22f;
                        margin-right: 8px;
                        font-size: 16px;
                        position: relative;
                        top: 2px;
                        }
                        .user-info-wrapper .open > .dropdown-menu a > .fa {
                        color: #89b22f;
                        min-width: 16px;
                        }
                        .user-info-wrapper .open > .dropdown-menu a:hover > .fa {
                        color: #fff;
                        min-width: 16px;
                        }
                        .user-info-wrapper .open > .dropdown-menu a:hover > .fa {
                        color: #fff;
                        }
                        .challan_search_wrapper {
                        background: #dddddd87;
                        padding-top: 10px;
                        }
                        .dataTables_empty {
                        font-family: sans-serif !important;
                        }
                        .deposit_section {
                        background: #dadbdf87;
                        padding: 10px 0;
                        margin-left: 0;
                        margin-right: 0;
                        }
                        .a2i_logo img {
                        max-height: 24px;
                        margin-top: -6px;
                        }
                        .social-links-bordered > li.a2i_logo_wrapper {
                        border-right: 1px solid #9e5bba;
                        padding-right: 12px;
                        }
                        .social-links-bordered > li.lang_form_wrapper {
                        border-left: 1px solid #9e5bba;
                        }
                        .custom-navbar .navbar-nav {
                        width: 100%;
                        background: #e3e6ef;
                        }
                        .custom-navbar .navbar-nav > li {
                        min-height: 37px;
                        -webkit-transition: all 0.2s linear;
                        -moz-transition: all 0.2s linear;
                        -o-transition: all 0.2s linear;
                        transition: all 0.2s linear;
                        }
                        .custom-navbar .navbar-nav > li > a {
                        color: #555;
                        display: block;
                        font-size: 17px;
                        line-height: 14px;
                        margin-top: 10px;
                        padding: 3px 12px 0;
                        border-right: 1px solid #9da2b3;
                        -webkit-transition: all 0.2s linear;
                        -moz-transition: all 0.2s linear;
                        -o-transition: all 0.2s linear;
                        transition: all 0.2s linear;
                        }
                        .custom-navbar .navbar-nav > li > a > .fa-home {
                        margin-right: 5px;
                        font-size: 18px;
                        line-height: 14px;
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li > a {
                        padding: 3px 7px 0;
                        }
                        .custom-navbar .navbar-nav > li.open > a .glyphicon-menu-right:before {
                        content: "\e259" !important;
                        }
                        .custom-navbar .navbar-nav > li:last-child > a,
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li:last-child > a {
                        border-right: none;
                        }
                        .custom-navbar .navbar-nav > li.active,
                        .custom-navbar .navbar-nav > li:hover {
                        background-color: #86bc42;
                        }
                        .custom-navbar .navbar-nav > li.active > a,
                        .custom-navbar .navbar-nav > li:hover > a {
                        color: #fff;
                        }
                        .custom-navbar .nav .open > a,
                        .custom-navbar .nav .open > a:focus,
                        .custom-navbar .nav .open > a:hover {
                        background-color: transparent;
                        border-color: #909b51;
                        }
                        .custom-navbar .nav > li > a:focus,
                        .custom-navbar .nav > li > a:hover {
                        background-color: transparent;
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li.active > a,
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li:hover > a {
                        border-right: 1px solid #358a17;
                        }
                        .custom-navbar .navbar-nav > li > .dropdown-menu {
                        background: #92bb38;
                        }
                        .custom-navbar .navbar-nav > li > .dropdown-menu li {
                        background: #fff;
                        border-bottom: 1px solid #ddd;
                        }
                        .custom-navbar .navbar-nav > li > .dropdown-menu li:last-child {
                        border-bottom: none;
                        }
                        .custom-navbar .navbar-nav > li > .dropdown-menu li a {
                        padding: 6px 20px;
                        }
                        .custom-navbar .navbar-nav > li > .dropdown-menu li:hover a {
                        color: #fff;
                        }
                        .custom-navbar .dropdown-menu > li.active > a,
                        .custom-navbar .dropdown-menu > li > a:focus,
                        .custom-navbar .dropdown-menu > li > a:hover {
                        color: #fff;
                        background-color: #92bb38;
                        }
                        .navbar-brand {
                        font-size: 24px;
                        }
                        .navbar-container {
                        padding: 0;
                        }
                        .navbar.navbar-fixed-top.fixed-theme {
                        min-height: 65px;
                        background-color: #e1e5f1;
                        border-bottom: 1px solid #dfe4f3;
                        box-shadow: 0 0 5px rgba(0, 0, 0, 0.75);
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav {
                        float: right !important;
                        margin-top: 13px;
                        max-width: 980px;
                        background-color: transparent;
                        border: 1px solid transparent;
                        }
                        .navbar-brand.fixed-theme {
                        font-size: 18px;
                        }
                        .navbar-container.fixed-theme {
                        padding: 0;
                        }
                        .navbar-brand,
                        .navbar-brand.fixed-theme,
                        .navbar-container,
                        .navbar-container.fixed-theme,
                        .navbar.navbar-fixed-top.fixed-theme {
                        transition: 0.4s;
                        -webkit-transition: 0.4s;
                        }
                        .logo-fixed-top {
                        display: none;
                        }
                        .navbar.navbar-fixed-top .logo-fixed-top {
                        display: block;
                        position: absolute;
                        left: -35px;
                        top: 7px;
                        color: #fff;
                        }
                        .navbar.navbar-fixed-top .navbar-collapse.collapse {
                        position: relative;
                        padding-left: 100px;
                        }
                        .navbar.navbar-fixed-top .logo-fixed-top img {
                        margin-right: 10px;
                        max-height: 50px;
                        }
                        .navbar.navbar-fixed-top .logo-fixed-top .slogan {
                        line-height: 35px;
                        }
                        .challan-history-wrapper .table td,
                        .challan-history-wrapper .table th {
                        padding: 8px 5px;
                        font-size: 13px;
                        }
                        #multiple-data-container > tbody > tr > td,
                        #multiple-data-container > tbody > tr > th,
                        #multiple-data-container > tfoot > tr > td,
                        #multiple-data-container > tfoot > tr > th,
                        #multiple-data-container > thead > tr > td,
                        #multiple-data-container > thead > tr > th {
                        vertical-align: middle;
                        }
                        #multiple-data-container #totalFeeSection {
                        background: rgba(221, 221, 221, 0.35);
                        }
                        #user_economic_code_subtype_multi option {
                        width: 200px;
                        }
                        .challan-menu li.parent-menu > a {
                        display: block;
                        background: #fafafa;
                        padding: 5px;
                        padding-top: 7px;
                        }
                        #challanByEcoCode .challan-menu li.parent-menu > a,
                        #challanByInstitute .challan-menu li.parent-menu > a {
                        text-align: center;
                        height: 60px;
                        vertical-align: middle;
                        display: table-cell;
                        width: inherit;
                        min-width: 83px;
                        }
                        #challanByEcoCode .challan-menu li.parent-menu > a.name > .new_notification,
                        #challanByEcoCode .challan-menu li.parent-menu > ul {
                        display: none;
                        }
                        #challanByEcoCode .challan-menu li#id_22.parent-menu:hover > ul {
                        display: block;
                        }
                        #challanByEcoCode
                        .challan-menu
                        li#id_22.parent-menu
                        > a.name
                        > .new_notification {
                        display: block;
                        }
                        #challanByEcoCode .challan-menu li.parent-menu:hover > ul,
                        #challanByInstitute .challan-menu li.parent-menu:hover > ul {
                        width: 92.5%;
                        top: 59px;
                        max-height: 300px;
                        overflow-y: auto;
                        }
                        #challanByInstitute .challan-menu li.parent-menu:hover > ul {
                        width: 92%;
                        overflow-y: visible;
                        max-height: inherit;
                        }
                        .challan-menu li.parent-menu > ul {
                        padding: 0;
                        display: none;
                        border: 1px solid #59ba37;
                        background: rgba(211, 234, 178, 0.8);
                        -webkit-box-shadow: 0 3px 5px rgba(89, 186, 55, 0.6);
                        -moz-box-shadow: 0 3px 5px rgba(89, 186, 55, 0.6);
                        box-shadow: 0 3px 5px rgba(89, 186, 55, 0.6);
                        }
                        .challan-menu li.parent-menu > ul > li {
                        position: relative;
                        border-bottom: 1px solid #9dd582;
                        }
                        .challan-menu li.parent-menu:hover > ul {
                        margin: 0;
                        width: 94.3%;
                        top: 94px;
                        display: block;
                        z-index: 9;
                        position: absolute;
                        list-style: none;
                        cursor: pointer;
                        }
                        .challan-menu li.parent-menu > ul > li > a {
                        color: #86bc42;
                        display: block;
                        font-size: 14px;
                        padding: 5px 8px;
                        border: 0 !important;
                        text-decoration: none;
                        }
                        .challan-menu li.parent-menu > ul > li ul > li.arrow > a::after,
                        .challan-menu li.parent-menu > ul > li.arrow > a::after {
                        position: absolute;
                        content: "";
                        top: 16px;
                        left: 100%;
                        -webkit-transform: translateY(-50%);
                        -moz-transform: translateY(-50%);
                        -o-transform: translateY(-50%);
                        -ms-transform: translateY(-50%);
                        transform: translateY(-50%);
                        margin-left: -10px;
                        width: 0;
                        height: 0;
                        border-top: 5px solid transparent;
                        border-bottom: 5px solid transparent;
                        border-left: 5px solid #2971018a;
                        }
                        .challan-menu li.parent-menu > ul > li:hover > a {
                        background: #fafafa;
                        letter-spacing: 0.2px;
                        }
                        .challan-menu li.parent-menu > ul li:hover > ul {
                        display: block;
                        position: absolute;
                        left: 100%;
                        top: 0;
                        width: auto;
                        }
                        .challan-menu li.parent-menu > ul li > ul {
                        display: none;
                        padding: 0;
                        background: rgba(211, 234, 178, 0.9);
                        -webkit-box-shadow: 0 3px 5px rgba(89, 186, 55, 0.6);
                        -moz-box-shadow: 0 3px 5px rgba(89, 186, 55, 0.6);
                        box-shadow: 0 3px 5px rgba(89, 186, 55, 0.6);
                        z-index: 9;
                        }
                        .challan-menu li.parent-menu > ul li:hover > ul {
                        display: block;
                        position: absolute;
                        left: 100%;
                        top: -1px;
                        width: auto;
                        min-width: 235px;
                        max-width: 250px;
                        max-height: 300px;
                        overflow-y: auto;
                        border: 1px solid #59ba37;
                        border-left-color: #59ba37;
                        }
                        #challanByInstitute .challan-menu li.parent-menu > ul li:hover > ul {
                        min-width: 180px;
                        }
                        .challan-menu li.parent-menu > ul li:hover ul.third_level {
                        overflow: inherit;
                        max-height: inherit;
                        }
                        .challan-menu li.parent-menu > ul > li ul > li {
                        display: block;
                        word-wrap: break-word;
                        border-bottom: 1px solid #9dd582;
                        position: relative;
                        }
                        .challan-menu li.parent-menu > ul > li ul > li:last-child,
                        .challan-menu li.parent-menu > ul > li:last-child {
                        border-bottom: none;
                        }
                        .challan-menu li.parent-menu > ul > li ul > li > a {
                        color: #86bc42;
                        font-size: 14px;
                        display: block;
                        padding: 5px 8px;
                        border: none !important;
                        text-decoration: none;
                        }
                        .challan-menu li.parent-menu > ul > li ul > li:hover > a {
                        color: #86bc42;
                        background: #fafafa;
                        letter-spacing: 0.2px;
                        }
                        .social-links-bordered > li {
                        padding-right: 0;
                        }
                        .social-links-bordered a.social-link {
                        width: 24px;
                        height: 24px;
                        color: #89b22f;
                        line-height: 22px;
                        text-align: center;
                        margin-right: 0;
                        background: #e1e5f1;
                        border-radius: 50%;
                        display: inline-block;
                        border: 1px solid #7db23b;
                        }
                        .social-links-bordered a.social-link .fa {
                        font-size: 14px;
                        line-height: 22px;
                        text-align: center;
                        }
                        .social-links-bordered a.social-link:hover {
                        color: #fff;
                        }
                        .social-links-bordered a.social-link.faicon-facebook:hover {
                        background-color: #3b5998;
                        border-color: #3b5998;
                        }
                        .social-links-bordered a.social-link.faicon-twitter:hover {
                        background-color: #00acee;
                        border-color: #00acee;
                        }
                        .social-links-bordered a.social-link.faicon-linkedin:hover {
                        background-color: #0e76a8;
                        border-color: #0e76a8;
                        }
                        .social-links-bordered a.social-link.faicon-google-plus:hover {
                        background-color: #db4a39;
                        border-color: #db4a39;
                        }
                        .slider-wrapper {
                        border: 4px solid #8c71b5;
                        }
                        .slider-wrapper .carousel-inner > .item > img {
                        width: 100%;
                        height: 250px;
                        }
                        .home-slider-wrapper .main-container {
                        padding: 0;
                        }
                        .home-slider-wrapper .item h3 {
                        padding: 0 20px;
                        color: #9e5bba;
                        font-size: 26px;
                        text-transform: uppercase;
                        font-weight: 700;
                        line-height: 48px;
                        text-align: center;
                        background: rgba(255, 255, 255, 0.7);
                        display: inline-block;
                        }
                        .home-slider-wrapper .item h4 {
                        color: #fff;
                        font-size: 22px;
                        text-transform: uppercase;
                        font-weight: 700;
                        text-align: center;
                        }
                        .home-slider-wrapper .carousel-indicators {
                        width: 40%;
                        bottom: -14px;
                        margin-left: -20%;
                        border-radius: 5px 5px 0 0;
                        background: rgba(147, 95, 193, 0.85);
                        }
                        .home-slider-wrapper .carousel-control.left,
                        .home-slider-wrapper .carousel-control.right {
                        background-image: none;
                        }
                        .home-slider-wrapper .carousel .item {
                        min-height: 200px;
                        max-height: 200px;
                        height: 100%;
                        width: 100%;
                        }
                        .home-slider-wrapper .carousel-inner .item .container-fluid {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        position: absolute;
                        bottom: 0;
                        top: 0;
                        left: 0;
                        right: 0;
                        padding: 0;
                        }
                        .home-slider-wrapper h3 {
                        animation-delay: 1s;
                        }
                        .home-slider-wrapper h4 {
                        animation-delay: 2s;
                        }
                        .home-slider-wrapper h2 {
                        animation-delay: 3s;
                        }
                        .home-slider-wrapper .carousel-control {
                        width: 6%;
                        text-shadow: none;
                        }
                        .home-slider-wrapper h1 {
                        text-align: center;
                        margin-bottom: 30px;
                        font-size: 30px;
                        font-weight: 700;
                        }
                        .home-slider-wrapper .p {
                        padding-top: 125px;
                        text-align: center;
                        }
                        .home-slider-wrapper .p a {
                        text-decoration: underline;
                        }
                        .home-slider-wrapper .carousel-indicators li {
                        width: 11px;
                        height: 11px;
                        background-color: rgba(255, 255, 255, 0.7);
                        border: none;
                        margin: 2px 5px 0 0;
                        }
                        .home-bottom-slider-wrapper .carousel-indicators {
                        bottom: -35px;
                        z-index: 0;
                        width: 100%;
                        left: 0;
                        margin-left: 0;
                        padding-left: 15px;
                        }
                        .home-bottom-slider-wrapper .carousel-indicators li {
                        float: left;
                        width: 75px;
                        height: 25px;
                        text-indent: 0;
                        color: #fff;
                        border: none;
                        margin: 0;
                        line-height: 27px;
                        font-size: 15px;
                        display: inline-block;
                        background-color: #59ba37;
                        }
                        .home-bottom-slider-wrapper .carousel-indicators li.next {
                        float: right;
                        }
                        .home-bottom-slider-wrapper .carousel-indicators .active,
                        .home-slider-wrapper .carousel-indicators .active {
                        width: 75px;
                        height: 25px;
                        border: none;
                        background-color: #8cc63f;
                        }
                        .home-bottom-slider-wrapper .carousel-indicators .active {
                        display: none;
                        }
                        .home-slider-wrapper .carousel-indicators .active {
                        width: 12px;
                        height: 12px;
                        margin-top: 3px;
                        }
                        #graph-slider .carousel-indicators li {
                        width: 12px;
                        height: 12px;
                        background-color: #8cc63f;
                        border: none;
                        margin: 0;
                        margin-bottom: 2px;
                        }
                        #graph-slider .carousel-indicators {
                        left: 0;
                        bottom: -11px;
                        height: 14px;
                        width: 100%;
                        z-index: 7;
                        margin-left: 0;
                        background: #e6e7ea;
                        }
                        #graph-slider .carousel-indicators .active {
                        background-color: #8b61c8;
                        }
                        .carousel-fade .carousel-inner .item {
                        -webkit-transition-property: opacity;
                        transition-property: opacity;
                        }
                        .carousel-fade .carousel-inner .active.left,
                        .carousel-fade .carousel-inner .active.right,
                        .carousel-fade .carousel-inner .item {
                        opacity: 0;
                        }
                        .carousel-fade .carousel-inner .active,
                        .carousel-fade .carousel-inner .next.left,
                        .carousel-fade .carousel-inner .prev.right {
                        opacity: 1;
                        }
                        .carousel-fade .carousel-inner .active.left,
                        .carousel-fade .carousel-inner .active.right,
                        .carousel-fade .carousel-inner .next,
                        .carousel-fade .carousel-inner .prev {
                        left: 0;
                        -webkit-transform: translate3d(0, 0, 0);
                        transform: translate3d(0, 0, 0);
                        }
                        .carousel-fade .carousel-control {
                        top: 101%;
                        height: 30px;
                        z-index: -1;
                        }
                        .home-bottom-slider-wrapper .carousel-control.left,
                        .home-bottom-slider-wrapper .carousel-control.right {
                        left: 15px;
                        width: 40px;
                        color: #fff;
                        background-image: none;
                        filter: none;
                        background: #9e5bba;
                        background-repeat: no-repeat;
                        }
                        .home-bottom-slider-wrapper .carousel-control.right {
                        right: 0;
                        left: auto;
                        }
                        .home-bottom-slider-wrapper .carousel-control .glyphicon {
                        top: 4px;
                        }
                        .home-bottom-slider-wrapper .carousel-control .glyphicon-chevron-left,
                        .home-bottom-slider-wrapper .carousel-control .glyphicon-chevron-right {
                        width: 20px;
                        height: 30px;
                        margin-right: 0;
                        margin-top: -16px;
                        font-size: 20px;
                        line-height: 30px;
                        }
                        .home-bottom-slider-wrapper .carousel-control .glyphicon-chevron-right {
                        right: 0;
                        }
                        .carousel-control .fa-angle-left,
                        .carousel-control .fa-angle-right {
                        position: absolute;
                        top: 50%;
                        z-index: 5;
                        display: inline-block;
                        }
                        .carousel-control .fa-angle-left {
                        left: 50%;
                        width: 38px;
                        height: 38px;
                        margin-top: -15px;
                        font-size: 30px;
                        color: #fff;
                        border: 3px solid #fff;
                        -webkit-border-radius: 23px;
                        -moz-border-radius: 23px;
                        border-radius: 53px;
                        }
                        .carousel-control .fa-angle-right {
                        right: 50%;
                        width: 38px;
                        height: 38px;
                        margin-top: -15px;
                        font-size: 30px;
                        color: #fff;
                        border: 3px solid #fff;
                        -webkit-border-radius: 23px;
                        -moz-border-radius: 23px;
                        border-radius: 53px;
                        }
                        .carousel-control {
                        opacity: 1;
                        }
                        .home-bottom-slider-wrapper .carousel-inner {
                        overflow: visible;
                        }
                        #first-slider .item {
                        background-size: cover;
                        background-repeat: no-repeat;
                        }
                        #first-slider .slide1 {
                        background-image: url(../img/slider/1.jpg);
                        }
                        #first-slider .slide2 {
                        background-image: url(../img/slider/2.jpg);
                        }
                        #first-slider .slide3 {
                        background-image: url(../img/slider/3.jpg);
                        }
                        #first-slider .slide4 {
                        background-image: url(../img/slider/4.jpg);
                        }
                        #first-slider .slide5 {
                        background-image: url(../img/slider/5.jpg);
                        }
                        #first-slider .slide6 {
                        background-image: url(../img/slider/6.jpg);
                        }
                        #first-slider .slide7 {
                        background-image: url(../img/slider/7.jpg);
                        }
                        #first-slider .slide8 {
                        background-image: url(../img/slider/8.jpg);
                        }
                        #first-slider .slide9 {
                        background-image: url(../img/slider/9.jpg);
                        }
                        #first-slider .slide10 {
                        background-image: url(../img/slider/10.jpg);
                        }
                        #first-slider .slide11 {
                        background-image: url(../img/slider/11.jpg);
                        }
                        #first-slider .slide12 {
                        background-image: url(../img/slider/12.jpg);
                        }
                        #first-slider .slide13 {
                        background-image: url(../img/slider/13.jpg);
                        }
                        #first-slider .slide14 {
                        background-image: url(../img/slider/14.jpg);
                        }
                        #first-slider .slide15 {
                        background-image: url(../img/slider/15.jpg);
                        }
                        #first-slider .slide16 {
                        background-image: url(../img/slider/16.jpg);
                        }
                        #first-slider .slide17 {
                        background-image: url(../img/slider/17.jpg);
                        }
                        #first-slider .slide18 {
                        background-image: url(../img/slider/17.jpg);
                        }
                        .homepage-tab-wrapper {
                        position: relative;
                        z-index: 9;
                        }
                        .homepage-tab-wrapper .nav-tabs {
                        border-bottom: none;
                        }
                        .main-wrapper .tab-content {
                        padding: 10px 0;
                        margin-top: 1px;
                        border-top: 2px solid #836abd;
                        }
                        .main-wrapper .tab-content.padding-bottom0 {
                        padding-bottom: 0;
                        }
                        .main-wrapper .registration-page-wrapper .tab-content {
                        border-top: none;
                        }
                        .registration-page-wrapper .nav > li > a {
                        text-decoration: none;
                        background-color: #eee;
                        }
                        .registration-page-wrapper .nav > li.active > a {
                        background-color: #fff;
                        }
                        .tab-content .tab-pane {
                        display: none;
                        }
                        .tab-content .active {
                        display: block;
                        }
                        .nav-tabs.tab-links > li > a {
                        color: #555;
                        font-size: 16px;
                        margin-right: 1px;
                        display: inline-block;
                        background: #fff;
                        font-weight: 400;
                        border: 1px solid #c7ccda;
                        padding: 9px 15px 5px 15px;
                        border-radius: 3px 3px 0 0;
                        }
                        .nav-tabs.tab-links > li > a:focus,
                        .nav-tabs.tab-links > li > a:hover,
                        ul.tab-links > li.active > a,
                        ul.tab-links > li.active > a:focus,
                        ul.tab-links > li.active > a:hover {
                        background: #e3e6ef;
                        }
                        .dropdown-menu.challan-type-dropdown {
                        min-width: 180px;
                        background: #e8e8e8;
                        }
                        .dropdown-menu.challan-type-dropdown > li > a {
                        background: #fff;
                        }
                        .dropdown-menu.challan-type-dropdown > .active > a,
                        .dropdown-menu.challan-type-dropdown > .active > a:focus,
                        .dropdown-menu.challan-type-dropdown > .active > a:hover,
                        .dropdown-menu.challan-type-dropdown > li > a:focus,
                        .dropdown-menu.challan-type-dropdown > li > a:hover {
                        color: #373737;
                        background-color: #e8e8e8;
                        }
                        .category-top {
                        position: relative;
                        text-align: center;
                        }
                        .card-wrapper.margin-r0 {
                        margin-right: 0;
                        }
                        .homepage-tab-wrapper .card-wrapper.margin-r0 {
                        margin-bottom: 0;
                        }
                        .challan-page-wrapper .homepage-tab-wrapper .card-wrapper.margin-r0 {
                        padding-right: 15px;
                        }
                        .challan-page-wrapper #challanByEcoCode .challan-menu li.parent-menu:hover > ul,
                        .challan-page-wrapper
                        #challanByInstitute
                        .challan-menu
                        li.parent-menu:hover
                        > ul {
                        width: 94.5%;
                        }
                        .challan-page-wrapper .coa_key_style {
                        max-width: 25%;
                        }
                        .card-wrapper .card {
                        margin-bottom: 15px;
                        padding-right: 0;
                        }
                        .homepage-tab-wrapper .card-wrapper .card {
                        padding-right: 0;
                        margin-bottom: 18px;
                        }
                        .card-wrapper .card a {
                        -webkit-transition: all 0.2s linear;
                        -moz-transition: all 0.2s linear;
                        -o-transition: all 0.2s linear;
                        transition: all 0.2s linear;
                        }
                        .homepage-tab-wrapper .card-wrapper .card > a {
                        border: 1px solid #e8e8e8;
                        }
                        .homepage-tab-wrapper .card-wrapper .card:hover > a {
                        border: 1px solid #59ba37;
                        -webkit-box-shadow: 0 1px 5px #ccc;
                        -moz-box-shadow: 0 1px 5px #ccc;
                        box-shadow: 0 1px 5px #ccc;
                        }
                        .challan-page-wrapper .card-wrapper .card a {
                        text-decoration: none;
                        }
                        .card-wrapper .card .name {
                        color: #86bc42;
                        min-height: 20px;
                        max-height: 20px;
                        overflow: hidden;
                        line-height: 20px;
                        display: inline-block;
                        }
                        .blank-form-wrapper.card-wrapper .card .name {
                        min-height: 32px;
                        max-height: 32px;
                        padding: 5px;
                        display: block;
                        font-size: 15px;
                        background: #eeeff1;
                        }
                        .blank-form-wrapper.card-wrapper .card a {
                        background: #fff;
                        text-decoration: none;
                        border: 1px solid #ddd;
                        }
                        .blank-form-wrapper.card-wrapper .card .image {
                        margin-top: 5px;
                        margin-bottom: 5px;
                        }
                        .blank-form-wrapper.card-wrapper .card .subcategory.text-center {
                        background: #efefef;
                        border-bottom: none;
                        }
                        .blank-form-wrapper.card-wrapper .card:hover a {
                        background: #fff;
                        border: 1px solid #59ba37;
                        }
                        .card-wrapper .card .name h5 {
                        font-size: 15px;
                        line-height: 20px;
                        }
                        .card-wrapper .card .image {
                        margin-bottom: 6px;
                        }
                        .card-wrapper .card .list-inline > li {
                        position: relative;
                        }
                        .db-icon {
                        padding: 0;
                        max-height: 50px;
                        }
                        .card-wrapper .card .subcategory {
                        font-size: 14px;
                        padding: 5px 8px;
                        line-height: 18px;
                        border-bottom: 3px solid #48b322;
                        -webkit-transition: all 0.2s linear;
                        -moz-transition: all 0.2s linear;
                        -o-transition: all 0.2s linear;
                        transition: all 0.2s linear;
                        }
                        .card-wrapper .card .subcategory.text-center {
                        height: 30px;
                        min-height: 20px;
                        font-size: 16px;
                        padding: 5px;
                        background: #fff;
                        }
                        #challanByEcoCode .carousel-inner,
                        #challanByInstitute .carousel-inner {
                        min-height: 150px;
                        }
                        .coa_key_style {
                        font-size: 14px;
                        line-height: 24px;
                        list-style-type: square;
                        }
                        .level_1_title {
                        border-bottom: 1px solid #ddd;
                        }
                        .level_1_title > span {
                        background: #8cc643 !important;
                        }
                        .level_2_title {
                        font-size: 16px;
                        line-height: 24px;
                        margin-bottom: 5px;
                        border-bottom: 1px solid #ddd;
                        }
                        #challanByInstitute .item_119,
                        #challanByInstitute .item_209 {
                        display: none;
                        }
                        .item ul.list-style-none .level_3_item_wrapper {
                        min-height: 70px;
                        }
                        .item ul.list-style-none li.level_3_item > a {
                        font-size: 14px;
                        }
                        #printChallan {
                        color: #000;
                        font-size: 14px;
                        font-family: SolaimanLipi, Arial, sans-serif !important;
                        }
                        .code_wrapper {
                        padding-left: 40px;
                        }
                        .code_wrapper > div {
                        float: left;
                        width: 45px;
                        height: 30px;
                        line-height: 24px;
                        text-align: center;
                        padding: 3px 5px;
                        border: 1px solid #555;
                        }
                        .code_wrapper > div.code_no {
                        width: 75px;
                        }
                        .code_wrapper > div.border-left0 {
                        border-left: 0;
                        }
                        .copy-wrapper {
                        top: 0;
                        right: 0;
                        position: absolute;
                        }
                        #wrapper-copy div {
                        height: 24px;
                        min-width: 105px;
                        line-height: 20px;
                        padding: 2px 10px;
                        margin-bottom: 3px;
                        border: 1px solid #333;
                        }
                        #wrapper-copy.small-box div {
                        min-width: 24px;
                        max-width: 24px;
                        margin-right: 5px;
                        }
                        .copy-wrapper-outer input[type="checkbox"] {
                        margin: 4px 4px 0;
                        width: 20px;
                        height: 20px;
                        }
                        .bar-code-wrapper {
                        top: -30px;
                        right: 0;
                        position: absolute;
                        }
                        .challan-content .table-bordered {
                        border: 1px solid #333;
                        }
                        .challan-content .table-bordered > tbody > tr > td,
                        .challan-content .table-bordered > thead > tr > th {
                        padding: 5px;
                        vertical-align: middle;
                        border: 1px solid #333;
                        }
                        table.table-bordered.dataTable tbody td,
                        table.table-bordered.dataTable tbody th {
                        vertical-align: middle;
                        }
                        .challan-content .multi-challan-wrapper .table-bordered > tbody > tr > td,
                        .challan-content .multi-challan-wrapper .table-bordered > thead > tr > th {
                        padding: 2px 3px;
                        font-weight: 400;
                        word-break: break-word;
                        }
                        .sill-box {
                        width: 100px;
                        height: 70px;
                        margin: 0 auto;
                        font-size: 15px;
                        line-height: 70px;
                        text-align: center;
                        border: 1px solid #333;
                        }
                        .coa-mapping-wrapper.table > tbody > tr > td {
                        border-top: none;
                        }
                        .organization-list li {
                        color: #2c7e45;
                        }
                        .organization-list li.selected,
                        .organization-list li:hover {
                        color: #fff;
                        background-color: #2d7f46;
                        }
                        .coa_org_mapping tbody tr.highlight {
                        background-color: #ecaf2f;
                        }
                        .treegrid-indent {
                        width: 16px;
                        height: 16px;
                        display: inline-block;
                        position: relative;
                        }
                        .treegrid-expander {
                        width: 16px;
                        height: 16px;
                        display: inline-block;
                        position: relative;
                        cursor: pointer;
                        }
                        .treegrid-expander-expanded {
                        background-image: url(../img/collapse.png);
                        }
                        .treegrid-expander-collapsed {
                        background-image: url(../img/expand.png);
                        }
                        .datepicker-dropdown {
                        top: 0;
                        left: 0;
                        padding: 4px;
                        border-radius: 10px;
                        }
                        .datepicker table {
                        margin: 0;
                        -webkit-touch-callout: none;
                        -webkit-user-select: none;
                        -khtml-user-select: none;
                        -moz-user-select: none;
                        -ms-user-select: none;
                        user-select: none;
                        }
                        .datepicker table tr td,
                        .datepicker table tr th {
                        text-align: center;
                        width: 30px;
                        height: 30px;
                        border-radius: 4px;
                        border: none;
                        color: #000;
                        }
                        .datepicker table tr td.day:hover,
                        .datepicker table tr td.focused {
                        background: #71b64d;
                        cursor: pointer;
                        }
                        .datepicker table tr td.new,
                        .datepicker table tr td.old {
                        color: #393339;
                        }
                        .datepicker table tr td.today {
                        color: #000;
                        background-color: rgba(113, 182, 77, 0.46);
                        border-color: #ffb76f;
                        }
                        .datepicker table tr td.today:hover {
                        color: #fff;
                        background-color: #48b322;
                        border-color: #f59e00;
                        }
                        .datepicker table tr td.active.active,
                        .datepicker table tr td.active.highlighted.active,
                        .datepicker table tr td.active.highlighted:active,
                        .datepicker table tr td.active:active,
                        .open > .dropdown-toggle.datepicker table tr td.active,
                        .open > .dropdown-toggle.datepicker table tr td.active.highlighted {
                        color: #fff;
                        background-color: #71b64d;
                        border-color: #285e8e;
                        }
                        .datepicker table tr td.active.active.focus,
                        .datepicker table tr td.active.active:focus,
                        .datepicker table tr td.active.active:hover,
                        .datepicker table tr td.active.highlighted.active.focus,
                        .datepicker table tr td.active.highlighted.active:focus,
                        .datepicker table tr td.active.highlighted.active:hover,
                        .datepicker table tr td.active.highlighted:active.focus,
                        .datepicker table tr td.active.highlighted:active:focus,
                        .datepicker table tr td.active.highlighted:active:hover,
                        .datepicker table tr td.active:active.focus,
                        .datepicker table tr td.active:active:focus,
                        .datepicker table tr td.active:active:hover,
                        .open > .dropdown-toggle.datepicker table tr td.active.focus,
                        .open > .dropdown-toggle.datepicker table tr td.active.highlighted.focus,
                        .open > .dropdown-toggle.datepicker table tr td.active.highlighted:focus,
                        .open > .dropdown-toggle.datepicker table tr td.active.highlighted:hover,
                        .open > .dropdown-toggle.datepicker table tr td.active:focus,
                        .open > .dropdown-toggle.datepicker table tr td.active:hover {
                        color: #fff;
                        background-color: #48b322;
                        border-color: #193c5a;
                        }
                        .datepicker table tr td.active.active,
                        .datepicker table tr td.active.highlighted.active,
                        .datepicker table tr td.active.highlighted:active,
                        .datepicker table tr td.active:active,
                        .open > .dropdown-toggle.datepicker table tr td.active,
                        .open > .dropdown-toggle.datepicker table tr td.active.highlighted {
                        color: #fff;
                        background-color: #71b64d;
                        border-color: #285e8e;
                        }
                        .datepicker table tr td.active.active.focus,
                        .datepicker table tr td.active.active:focus,
                        .datepicker table tr td.active.active:hover,
                        .datepicker table tr td.active.highlighted.active.focus,
                        .datepicker table tr td.active.highlighted.active:focus,
                        .datepicker table tr td.active.highlighted.active:hover,
                        .datepicker table tr td.active.highlighted:active.focus,
                        .datepicker table tr td.active.highlighted:active:focus,
                        .datepicker table tr td.active.highlighted:active:hover,
                        .datepicker table tr td.active:active.focus,
                        .datepicker table tr td.active:active:focus,
                        .datepicker table tr td.active:active:hover,
                        .open > .dropdown-toggle.datepicker table tr td.active.focus,
                        .open > .dropdown-toggle.datepicker table tr td.active.highlighted.focus,
                        .open > .dropdown-toggle.datepicker table tr td.active.highlighted:focus,
                        .open > .dropdown-toggle.datepicker table tr td.active.highlighted:hover,
                        .open > .dropdown-toggle.datepicker table tr td.active:focus,
                        .open > .dropdown-toggle.datepicker table tr td.active:hover {
                        color: #fff;
                        background-color: #48b322;
                        border-color: #193c5a;
                        }
                        .datepicker table tr td span {
                        display: inline-block;
                        width: 62px;
                        height: 40px;
                        line-height: 40px;
                        margin: 2px 1.5px;
                        cursor: pointer;
                        border-radius: 4px;
                        }
                        .datepicker table tr td span.active {
                        background-color: #71b64d;
                        color: #fff;
                        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
                        }
                        .datepicker table tr td span:hover {
                        color: #fff;
                        background-color: #48b322;
                        }
                        .datepicker table td.today {
                        position: relative;
                        }
                        .datepicker .datepicker-switch {
                        font-family: Optima;
                        text-transform: uppercase;
                        font-size: 16px;
                        width: 145px;
                        }
                        .datepicker .datepicker-switch:hover,
                        .datepicker .next:hover,
                        .datepicker .prev:hover,
                        .datepicker tfoot tr th:hover {
                        background: #0b700b;
                        color: #fff;
                        }
                        .custom-list-type {
                        margin-bottom: 0;
                        padding-left: 15px;
                        list-style-type: bengali;
                        }
                        .custom-list-type li {
                        margin-bottom: 3px;
                        font-size: 14px;
                        line-height: 20px;
                        }
                        .list-with-bg-img li > a {
                        display: block;
                        border: 1px solid #eee;
                        }
                        .list-with-bg-img li > a span {
                        left: 70px;
                        top: 16px;
                        font-size: 16px;
                        color: #fff;
                        }
                        .sidebar-list li {
                        background: #9e5bba;
                        min-height: 20px;
                        line-height: 20px;
                        display: block;
                        padding: 10px;
                        }
                        .sidebar-list li a {
                        text-decoration: none;
                        }
                        .sidebar-list li span {
                        top: 3px;
                        color: #fff;
                        font-size: 17px;
                        position: relative;
                        }
                        form .field {
                        width: 100%;
                        position: relative;
                        margin-bottom: 15px;
                        }
                        form .field label {
                        text-transform: uppercase;
                        position: absolute;
                        top: 0;
                        left: 0;
                        background: #92bb38;
                        width: 100%;
                        color: #fff;
                        padding: 9px 0;
                        font-size: 18px;
                        text-align: center;
                        letter-spacing: 0.075em;
                        -webkit-transition: all 333ms ease-in-out;
                        -moz-transition: all 333ms ease-in-out;
                        -o-transition: all 333ms ease-in-out;
                        -ms-transition: all 333ms ease-in-out;
                        transition: all 333ms ease-in-out;
                        }
                        form .field label + span {
                        opacity: 0;
                        color: #fff;
                        display: block;
                        position: absolute;
                        top: 12px;
                        left: 7%;
                        font-size: 2.5em;
                        text-shadow: 1px 2px 0 #cd6302;
                        -webkit-transition: all 333ms ease-in-out;
                        -moz-transition: all 333ms ease-in-out;
                        -o-transition: all 333ms ease-in-out;
                        -ms-transition: all 333ms ease-in-out;
                        transition: all 333ms ease-in-out;
                        }
                        form .field input[type="email"],
                        form .field input[type="text"],
                        form .field textarea {
                        border: none;
                        background: #e8e9ea;
                        width: 100%;
                        margin: 0;
                        padding: 11px 0;
                        padding-left: 19.5%;
                        color: #313a3d;
                        font-size: 1em;
                        letter-spacing: 0.05em;
                        }
                        form .field input[type="email"]#msg,
                        form .field input[type="text"]#msg,
                        form .field textarea#msg {
                        height: 44px;
                        resize: none;
                        -webkit-transition: all 333ms ease-in-out;
                        -moz-transition: all 333ms ease-in-out;
                        -o-transition: all 333ms ease-in-out;
                        -ms-transition: all 333ms ease-in-out;
                        transition: all 333ms ease-in-out;
                        }
                        form .field input[type="email"].focused,
                        form .field input[type="email"]:focus,
                        form .field input[type="text"].focused,
                        form .field input[type="text"]:focus,
                        form .field textarea.focused,
                        form .field textarea:focus {
                        outline: 0;
                        }
                        form .field input[type="email"].focused#msg,
                        form .field input[type="email"]:focus#msg,
                        form .field input[type="text"].focused#msg,
                        form .field input[type="text"]:focus#msg,
                        form .field textarea.focused#msg,
                        form .field textarea:focus#msg {
                        padding-bottom: 150px;
                        }
                        form .field input[type="email"].focused + label,
                        form .field input[type="email"]:focus + label,
                        form .field input[type="text"].focused + label,
                        form .field input[type="text"]:focus + label,
                        form .field textarea.focused + label,
                        form .field textarea:focus + label {
                        width: 18%;
                        background: #683091;
                        color: #fff;
                        }
                        form .field:hover label {
                        width: 18%;
                        background: #9e5bba;
                        color: #fff;
                        }
                        form input[type="submit"] {
                        background: #9e5bba;
                        color: #fff;
                        -webkit-appearance: none;
                        border: none;
                        text-transform: uppercase;
                        position: relative;
                        padding: 13px 50px;
                        font-size: 1.4em;
                        letter-spacing: 0.1em;
                        font-family: Lato, sans-serif;
                        font-weight: 300;
                        -webkit-transition: all 333ms ease-in-out;
                        -moz-transition: all 333ms ease-in-out;
                        -o-transition: all 333ms ease-in-out;
                        -ms-transition: all 333ms ease-in-out;
                        transition: all 333ms ease-in-out;
                        }
                        form input[type="submit"]:hover {
                        background: #683091;
                        color: #fff;
                        }
                        form input[type="submit"]:focus {
                        outline: 0;
                        background: #683091;
                        }
                        .pay-online-content li img {
                        padding: 10px;
                        max-height: 117px;
                        -webkit-transition: all 333ms ease-in-out;
                        -moz-transition: all 333ms ease-in-out;
                        -o-transition: all 333ms ease-in-out;
                        -ms-transition: all 333ms ease-in-out;
                        transition: all 333ms ease-in-out;
                        }
                        .pay-online-content li:hover img {
                        -webkit-transition: all 333ms ease-in-out;
                        -moz-transition: all 333ms ease-in-out;
                        -o-transition: all 333ms ease-in-out;
                        -ms-transition: all 333ms ease-in-out;
                        transition: all 333ms ease-in-out;
                        -webkit-box-shadow: 0 2px 4px 0 rgba(46, 61, 73, 0.2);
                        -moz-box-shadow: 0 2px 4px 0 rgba(46, 61, 73, 0.2);
                        box-shadow: 0 2px 4px 0 rgba(46, 61, 73, 0.2);
                        }
                        .custom-panel .panel-footer {
                        padding: 5px;
                        }
                        .custom-panel .panel-footer .pagination > li > a,
                        .custom-panel .panel-footer .pagination > li > span {
                        padding: 4px 8px 0;
                        }
                        #scrollerNews {
                        height: 120px !important;
                        }
                        #scrollerNews .news-item {
                        padding: 0;
                        margin: 0;
                        line-height: 20px;
                        list-style: square !important;
                        }
                        .scroll-top {
                        right: 30px;
                        bottom: 20px;
                        width: 45px;
                        height: 45px;
                        cursor: pointer;
                        z-index: 9999;
                        position: fixed;
                        border-radius: 100%;
                        border: 5px solid #48b322;
                        background: rgba(72, 179, 34, 0.75);
                        -webkit-box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                        -moz-box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                        -o-box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
                        -webkit-transition: all 0.2s linear;
                        -moz-transition: all 0.2s linear;
                        -o-transition: all 0.2s linear;
                        transition: all 0.2s linear;
                        }
                        .scroll-top:hover {
                        background: #683091;
                        border-color: #683091;
                        }
                        .scroll-top:hover i {
                        color: #fff;
                        }
                        .scroll-top i {
                        padding: 1px 8px;
                        color: #fff;
                        width: 32px;
                        height: 32px;
                        }
                        #loader {
                        position: fixed;
                        background: #fff;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        z-index: 9999999999;
                        }
                        .square-spin {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        margin-left: -40px;
                        margin-top: -40px;
                        }
                        .square-spin img {
                        max-width: 64px;
                        }
                        #footer-wrapper {
                        width: 100%;
                        float: left;
                        color: #333;
                        background-color: #ebebeb;
                        border-bottom: 5px solid #683091;
                        }
                        #footer-menu {
                        padding: 25px 0 10px;
                        }
                        .footer-left > ul {
                        margin-left: -10px;
                        }
                        #footer-menu ul li {
                        line-height: 24px;
                        margin-bottom: 0;
                        padding: 0;
                        }
                        #footer-menu ul li a {
                        line-height: 16px;
                        padding: 0 10px;
                        border-right: 1px solid #ccc;
                        }
                        #footer-menu ul li:last-child a {
                        border-right: none;
                        }
                        text.highcharts-credits {
                        display: none;
                        }
                        .ui-widget.ui-widget-content {
                        font-size: 12px;
                        width: 240px !important;
                        max-height: 300px;
                        overflow-y: auto;
                        overflow-x: hidden;
                        }
                        .has-success .form-control-feedback {
                        color: #89b22f;
                        }
                        .highcharts-title {
                        font-size: 14px !important;
                        }
                        .languageSelectorArea {
                        min-width: 200px;
                        }
                        .languageSelectorArea .radio {
                        margin: 0;
                        font-size: 15px;
                        line-height: 21px;
                        display: inline-block;
                        }
                        .languageSelectorArea .form-control {
                        width: auto;
                        height: 13px;
                        line-height: 20px;
                        margin-left: -12px !important;
                        }
                        .highcharts-menu {
                        padding: 0 !important;
                        }
                        .highcharts-menu hr {
                        margin: 2px 0;
                        }
                        @media only all and (max-width: 767px) {
                        .main-wrapper {
                            width: 100%;
                        }
                        .header-top-bar {
                            font-size: 12px;
                            padding: 6px 10px;
                            line-height: 22px;
                        }
                        .social-links-bordered a.social-link {
                            width: 22px;
                            height: 22px;
                            line-height: 20px;
                        }
                        form#searchForm {
                            padding-left: 0;
                            padding-right: 95px;
                        }
                        form#searchForm .padding-r0 {
                            padding: 0;
                        }
                        .header-wrapper .search-cart-wrap {
                            width: 100%;
                        }
                        .user-info-wrapper {
                            padding: 0;
                            padding-left: 15px;
                        }
                        .user-info-wrapper li {
                            float: left;
                            margin-right: 5px;
                            display: inline-block !important;
                        }
                        .user-info-wrapper a {
                            padding: 5px 10px;
                        }
                        .home-login_wrapper .btn-default.custom-btn {
                            float: left;
                        }
                        .navbar.navbar-fixed-top.fixed-theme {
                            min-height: 52px;
                        }
                        .custom-navbar .navbar-nav > li {
                            min-height: 33px;
                        }
                        .navbar-nav {
                            margin: 0;
                        }
                        .navbar.custom-navbar {
                            min-height: 2px;
                            margin-bottom: 10px;
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav {
                            margin: 2px 0 0 0;
                            background: #89b22f;
                        }
                        .navbar-brand {
                            display: none !important;
                        }
                        .navbar-brand.fixed-theme {
                            display: block !important;
                            font-size: 18px;
                            padding: 5px 20px;
                        }
                        .logo_wrapper img {
                            max-height: 60px;
                        }
                        .top-user-row .left-corner {
                            background: 0 0;
                        }
                        .custom-navbar .navbar-nav {
                            background-color: #89b22f;
                        }
                        .navbar.navbar-fixed-top .logo-title {
                            font-size: 22px;
                            margin: 14px 0 0 0;
                        }
                        .navbar.custom-navbar .navbar-collapse {
                            margin: 0;
                        }
                        .navbar.custom-navbar .navbar-collapse.collapse {
                            position: relative;
                            padding-left: 0;
                            margin: 0;
                        }
                        .navbar.navbar-fixed-top .logo-fixed-top {
                            display: none;
                        }
                        .custom-navbar .navbar-nav > li > a {
                            color: #fff;
                            font-size: 14px;
                            line-height: 16px;
                            margin-top: 0;
                            padding: 8px 15px;
                            border-right: none;
                            border-bottom: 1px solid #f8f8f9;
                        }
                        .custom-navbar .navbar-nav > li:last-child a {
                            border-bottom: none;
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li > a {
                            border-right: none;
                            padding: 8px 15px;
                            border-bottom: 1px solid #8cc643;
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li:last-child > a {
                            border-bottom: 0;
                        }
                        .navbar-toggle {
                            margin-top: -79px;
                            margin-right: 25px;
                            background-color: #89b22f;
                        }
                        .table-responsive > .table-bordered {
                            min-width: 700px;
                        }
                        .challan-history-wrapper #grid_list.table {
                            min-width: 1000px;
                        }
                        .navbar-toggle.margin-top-10 {
                            margin-top: -78px;
                            margin-right: 30px;
                        }
                        .navbar-container.fixed-theme .navbar-toggle {
                            margin-top: 8px;
                            margin-right: 23px;
                            background-color: #89b22f;
                        }
                        .custom-navbar .navbar-nav > li > .dropdown-menu {
                            padding: 0;
                        }
                        .custom-navbar .navbar-nav > li.active,
                        .custom-navbar .navbar-nav > li:hover {
                            background: 0 0;
                        }
                        .navbar-toggle .icon-bar {
                            background: #fff;
                        }
                        .navbar-brand > img {
                            display: block;
                            max-height: 42px;
                        }
                        .home-slider-wrapper .carousel-indicators {
                            width: 98%;
                            margin-left: -49%;
                        }
                        .home-slider-wrapper .carousel-indicators li {
                            width: 8px;
                            height: 8px;
                        }
                        .home-slider-wrapper .carousel-indicators .active {
                            width: 9px;
                            height: 9px;
                        }
                        .slider-wrapper .carousel-inner > .item > img {
                            height: auto;
                        }
                        .home-bottom-slider-wrapper .carousel-control.left {
                            left: 0;
                            width: 25px;
                        }
                        .home-bottom-slider-wrapper .carousel-control.right {
                            width: 25px;
                            right: -15px;
                        }
                        .carousel-fade .carousel-control {
                            top: 48%;
                            height: 30px;
                            z-index: 1;
                        }
                        .homepage-tab-wrapper #challanByEcoCode .card-wrapper .card {
                            display: inline-block;
                            width: 49%;
                        }
                        #challanByEcoCode .challan-menu li.parent-menu > a,
                        #challanByInstitute .challan-menu li.parent-menu > a {
                            font-size: 14px;
                            line-height: 45px;
                            display: inline-table;
                            width: 100%;
                            min-width: 50%;
                        }
                        #addChalanForm .margin-bottom-20 {
                            margin-bottom: 0;
                        }
                        #grid_list_wrapper .col-lg-6 {
                            padding-left: 0;
                            padding-right: 0;
                            margin-top: 10px;
                        }
                        #grid_list_wrapper.dataTables_wrapper div.dataTables_filter label,
                        #grid_list_wrapper.dataTables_wrapper div.dataTables_length label {
                            width: 100%;
                        }
                        #sidebar.padding-l0 {
                            padding-left: 15px;
                        }
                        #footer-menu ul li,
                        .credit-org,
                        .developed_by {
                            width: 100%;
                            text-align: center;
                        }
                        .credit-org {
                            margin-top: 10px;
                        }
                        .credit-org img {
                            max-height: 30px;
                        }
                        #footer-menu ul li a {
                            border-right: none;
                        }
                        }
                        @media only all and (max-width: 479px) {
                        .logo_wrapper {
                            padding: 0 5px;
                        }
                        .logo_wrapper img {
                            max-height: 45px;
                        }
                        .logo-title {
                            margin: 5px 0 0 0;
                        }
                        .logo-subtitle {
                            font-size: 82%;
                        }
                        .nav-tabs.tab-links > li > a,
                        .panel-title {
                            font-size: 14px;
                        }
                        .nav-tabs.tab-links > li > a {
                            font-size: 12px;
                            padding: 8px 5px 5px 5px;
                        }
                        .card-wrapper .card .subcategory {
                            font-size: 13px;
                        }
                        }
                        @media (min-width: 768px) {
                        .form-inline.home-login_wrapper .form-control {
                            max-width: 150px;
                        }
                        }
                        @media only all and (min-width: 768px) and (max-width: 991px) {
                        .main-wrapper {
                            width: 100%;
                        }
                        .logo_wrapper {
                            padding-left: 5px;
                            margin-top: 16px;
                            margin-bottom: 16px;
                        }
                        .logo_wrapper img {
                            max-height: 60px;
                        }
                        .logo-title {
                            font-size: 24px;
                            margin: 13px 0 0 0;
                        }
                        .header-right .form-inline .form-control {
                            max-width: 125px;
                            padding: 6px;
                            font-size: 12px;
                        }
                        .header-right .form-inline .form-control.top-search {
                            max-width: 100%;
                        }
                        .header-right .form-inline .btn-default.custom-btn {
                            min-width: 75px;
                        }
                        .header-right .form-inline .btn-default.search-btn {
                            min-width: 40px;
                        }
                        .header-right a {
                            font-size: 13px;
                        }
                        .navbar.navbar-fixed-top .logo-fixed-top {
                            left: 0;
                        }
                        .custom-navbar .navbar-nav > li > a {
                            font-size: 12px;
                            padding: 3px 5px 0;
                        }
                        .navbar.navbar-fixed-top .navbar-collapse.collapse {
                            padding-left: 55px;
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li > a {
                            font-size: 11px;
                            padding: 3px 4px 0;
                        }
                        .slider-wrapper .carousel-inner > .item > img {
                            height: auto;
                        }
                        .nav-tabs.tab-links > li > a {
                            font-size: 12px;
                            padding: 9px 5px 5px 4px;
                        }
                        .sidebar-offcanvas .custom-panel a {
                            font-size: 13px;
                        }
                        .pay-online-content li img {
                            max-height: 100px;
                        }
                        }
                        @media only screen and (max-width: 960px) {
                        .footer-artwork {
                            display: none;
                        }
                        }
                        @media only all and (min-width: 992px) and (max-width: 1199px) {
                        .main-wrapper {
                            width: 90%;
                        }
                        .navbar.navbar-fixed-top .logo-fixed-top {
                            left: 15px;
                        }
                        .navbar.navbar-fixed-top .navbar-collapse.collapse {
                            padding-left: 165px;
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li > a {
                            font-size: 13px;
                            padding: 3px 5px 0;
                        }
                        .custom-navbar .navbar-nav > li > a {
                            font-size: 13px;
                        }
                        .form-inline.home-login_wrapper .form-control {
                            max-width: 115px;
                        }
                        .user-info-wrapper a {
                            font-size: 13px;
                            padding: 0 8px;
                        }
                        .panel-title {
                            font-size: 14px;
                        }
                        .footer-left > ul {
                            margin-left: -5px;
                        }
                        #footer-menu ul li a {
                            padding: 0 5px;
                        }
                        #footer-menu ul li a,
                        .credit-org {
                            font-size: 13px;
                        }
                        }
                        @media only all and (min-width: 992px) and (max-width: 1091px) {
                        .card-wrapper .card:hover .subcategory {
                            width: 95.3%;
                        }
                        }
                        @media only all and (min-width: 1092px) and (max-width: 1199px) {
                        .main-wrapper {
                            width: 85%;
                        }
                        .navbar.navbar-fixed-top.custom-navbar .navbar-nav > li > a {
                            font-size: 14px;
                            padding: 3px 6px 0;
                        }
                        .card-wrapper .card:hover .subcategory {
                            width: 95.5%;
                        }
                        }
                        @media print {
                        #printChallan {
                            font-size: 12px !important;
                        }
                        .challan-content .table-bordered {
                            border: 1px solid #555;
                        }
                        .challan-content .table-bordered > tbody > tr > td,
                        .challan-content .table-bordered > thead > tr > th {
                            padding: 2px;
                            font-size: 10px;
                            font-weight: 400;
                        }
                        .copy-wrapper {
                            margin-top: 0;
                        }
                        #wrapper-copy div {
                            width: 90px;
                            padding: 3px;
                            font-size: 12px;
                            border: 1px solid #333;
                        }
                        .bar-code-wrapper {
                            text-align: right;
                        }
                        .bar-code-wrapper > img {
                            float: right;
                        }
                        .challan-content .table-bordered > thead > tr > th {
                            vertical-align: top;
                        }
                        .challan-content .table-bordered > tbody > tr > td,
                        .challan-content .table-bordered > thead > tr > th {
                            padding: 5px;
                            font-size: 13px;
                            font-weight: 400 !important;
                            vertical-align: top;
                            border: 1px solid #555;
                        }
                        }
                        .new_notification {
                        max-width: 60px;
                        position: absolute;
                        right: 0;
                        top: 0;
                        }
                        #FAQ_CONTAINER .panel-heading {
                        padding: 5px 15px;
                        }
                        #FAQ_CONTAINER .panel-heading [data-toggle="collapse"]:before {
                        color: #99c438;
                        }
                        #FAQ_CONTAINER .panel-heading [data-toggle="collapse"]:after {
                        font-family: FontAwesome;
                        content: "\f078";
                        float: right;
                        color: #99c438;
                        font-size: 18px;
                        line-height: 22px;
                        }
                        #FAQ_CONTAINER .panel-heading [data-toggle="collapse"].collapsed:after {
                        color: #99c438;
                        }
                        .sign_section {
                        padding: 5px;
                        font-size: 12px;
                        border: 1px dotted #000;
                        border-bottom: none;
                        }
                        .pagination > .active > a,
                        .pagination > .active > a:focus,
                        .pagination > .active > a:hover,
                        .pagination > .active > span,
                        .pagination > .active > span:focus,
                        .pagination > .active > span:hover {
                        background-color: #86bc42;
                        border-color: #86bc42;
                        }
                        .pagination > li > a,
                        .pagination > li > span {
                        color: #323137;
                        }
                        table.dataTable {
                        clear: both;
                        margin-top: 6px !important;
                        margin-bottom: 6px !important;
                        max-width: none !important;
                        border-collapse: separate !important;
                        }
                        table.dataTable td,
                        table.dataTable th {
                        -webkit-box-sizing: content-box;
                        box-sizing: content-box;
                        }
                        table.dataTable td.dataTables_empty,
                        table.dataTable th.dataTables_empty {
                        text-align: center;
                        }
                        table.dataTable.nowrap td,
                        table.dataTable.nowrap th {
                        white-space: nowrap;
                        }
                        div.dataTables_wrapper div.dataTables_length label {
                        font-weight: 400;
                        text-align: left;
                        white-space: nowrap;
                        }
                        div.dataTables_wrapper div.dataTables_length select {
                        width: 75px;
                        display: inline-block;
                        }
                        div.dataTables_wrapper div.dataTables_filter {
                        text-align: right;
                        }
                        div.dataTables_wrapper div.dataTables_filter label {
                        font-weight: 400;
                        white-space: nowrap;
                        text-align: left;
                        }
                        div.dataTables_wrapper div.dataTables_filter input {
                        margin-left: 0.5em;
                        display: inline-block;
                        width: auto;
                        }
                        div.dataTables_wrapper div.dataTables_info {
                        padding-top: 8px;
                        white-space: nowrap;
                        }
                        div.dataTables_wrapper div.dataTables_paginate {
                        margin: 0;
                        white-space: nowrap;
                        text-align: right;
                        }
                        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
                        margin: 2px 0;
                        white-space: nowrap;
                        }
                        div.dataTables_wrapper div.dataTables_processing {
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        width: 200px;
                        margin-left: -100px;
                        margin-top: -26px;
                        text-align: center;
                        padding: 1em 0;
                        }
                        table.dataTable thead > tr > td.sorting,
                        table.dataTable thead > tr > td.sorting_asc,
                        table.dataTable thead > tr > td.sorting_desc,
                        table.dataTable thead > tr > th.sorting,
                        table.dataTable thead > tr > th.sorting_asc,
                        table.dataTable thead > tr > th.sorting_desc {
                        padding-right: 30px;
                        }
                        table.dataTable thead > tr > td:active,
                        table.dataTable thead > tr > th:active {
                        outline: 0;
                        }
                        table.dataTable thead .sorting,
                        table.dataTable thead .sorting_asc,
                        table.dataTable thead .sorting_asc_disabled,
                        table.dataTable thead .sorting_desc,
                        table.dataTable thead .sorting_desc_disabled {
                        cursor: pointer;
                        position: relative;
                        }
                        table.dataTable thead .sorting:after,
                        table.dataTable thead .sorting_asc:after,
                        table.dataTable thead .sorting_asc_disabled:after,
                        table.dataTable thead .sorting_desc:after,
                        table.dataTable thead .sorting_desc_disabled:after {
                        position: absolute;
                        bottom: 8px;
                        right: 8px;
                        display: block;
                        font-family: "Glyphicons Halflings";
                        opacity: 0.5;
                        }
                        table.dataTable thead .sorting:after {
                        opacity: 0.2;
                        content: "\e150";
                        }
                        table.dataTable thead .sorting_asc:after {
                        content: "\e155";
                        }
                        table.dataTable thead .sorting_desc:after {
                        content: "\e156";
                        }
                        table.dataTable thead .sorting_asc_disabled:after,
                        table.dataTable thead .sorting_desc_disabled:after {
                        color: #eee;
                        }
                        div.dataTables_scrollHead table.dataTable {
                        margin-bottom: 0 !important;
                        }
                        div.dataTables_scrollBody > table {
                        border-top: none;
                        margin-top: 0 !important;
                        margin-bottom: 0 !important;
                        }
                        div.dataTables_scrollBody > table > thead .sorting:after,
                        div.dataTables_scrollBody > table > thead .sorting_asc:after,
                        div.dataTables_scrollBody > table > thead .sorting_desc:after {
                        display: none;
                        }
                        div.dataTables_scrollBody > table > tbody > tr:first-child > td,
                        div.dataTables_scrollBody > table > tbody > tr:first-child > th {
                        border-top: none;
                        }
                        div.dataTables_scrollFoot > .dataTables_scrollFootInner {
                        box-sizing: content-box;
                        }
                        div.dataTables_scrollFoot > .dataTables_scrollFootInner > table {
                        margin-top: 0 !important;
                        border-top: none;
                        }
                        @media screen and (max-width: 767px) {
                        div.dataTables_wrapper div.dataTables_filter,
                        div.dataTables_wrapper div.dataTables_info,
                        div.dataTables_wrapper div.dataTables_length,
                        div.dataTables_wrapper div.dataTables_paginate {
                            text-align: center;
                        }
                        }
                        table.dataTable.table-condensed > thead > tr > th {
                        padding-right: 20px;
                        }
                        table.dataTable.table-condensed .sorting:after,
                        table.dataTable.table-condensed .sorting_asc:after,
                        table.dataTable.table-condensed .sorting_desc:after {
                        top: 6px;
                        right: 6px;
                        }
                        table.table-bordered.dataTable td,
                        table.table-bordered.dataTable th {
                        border-left-width: 0;
                        }
                        table.table-bordered.dataTable td:last-child,
                        table.table-bordered.dataTable th:last-child {
                        border-right-width: 0;
                        }
                        table.table-bordered.dataTable tbody td,
                        table.table-bordered.dataTable tbody th {
                        border-bottom-width: 0;
                        }
                        div.dataTables_scrollHead table.table-bordered {
                        border-bottom-width: 0;
                        }
                        div.table-responsive > div.dataTables_wrapper > div.row {
                        margin: 0;
                        }
                        div.table-responsive
                        > div.dataTables_wrapper
                        > div.row
                        > div[class^="col-"]:first-child {
                        padding-left: 0;
                        }
                        div.table-responsive
                        > div.dataTables_wrapper
                        > div.row
                        > div[class^="col-"]:last-child {
                        padding-right: 0;
                        }

                    </style>
                    <div class="row">
                        <div class="col-xs-12 ">
                            <div id="printChallan" class="multi-challan-wrapper clearfix">
                            
                            <table class="col-xs-12 multi-challan-content padding0 clearfix">
                                <tbody>
                                <tr>
                                    <td>
                                    <table
                                        class="challan-top-content-wrapper col-xs-12 padding0 clearfix"
                                    >
                                        <tbody>
                                        <tr>
                                            <td class="width30p verticle-align-top">
                                            <span>তারিখঃ</span>
                                            <span
                                                class="font-family-boishkhi"
                                                style="
                                                width: 80px;
                                                display: inline-block;
                                                border-bottom: 1px dotted #000;
                                                "
                                            >'.date('d/m/Y').' </span
                                            ><br />
                                            টি আর ফরম নং- ৬ (২)<br />
                                            (এস আর অনুচ্ছেদ ৩৭ দ্রষ্টব্য) <br />
                                            </td>
                                            <td
                                            class="
                                                width45p
                                                text-center
                                                verticle-align-top
                                                position-relative
                                            "
                                            >
                                            <h3 class="margin-top0 margin-bottom-5">চালান ফরম</h3>
                                            <p class="margin-bottom-5">
                                                (প্রকাশনা ক্রয় ফি বাবদ জমার জন্য)
                                            </p>
                                            
                                            </td>
                                            <td class="width25p verticle-align-top">
                                            <table class="copy-wrapper-outer pull-right">
                                                <tbody>
                                                <tr>
                                                    <td>
                                                    <div id="wrapper-copy" class="small-box">
                                                        <div></div>
                                                    </div>
                                                    </td>
                                                    <td>
                                                    <div
                                                        id="wrapper-copy"
                                                        class="copy-wrapper222 pull-right"
                                                    >
                                                        <div>১ম ( মূল ) কপি</div>
                                                    </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                    <div id="wrapper-copy" class="small-box">
                                                        <div></div>
                                                    </div>
                                                    </td>
                                                    <td>
                                                    <div
                                                        id="wrapper-copy"
                                                        class="copy-wrapper222 pull-right"
                                                    >
                                                        <div>২য় কপি</div>
                                                    </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                    <div id="wrapper-copy" class="small-box">
                                                        <div></div>
                                                    </div>
                                                    </td>
                                                    <td>
                                                    <div
                                                        id="wrapper-copy"
                                                        class="copy-wrapper222 pull-right"
                                                    >
                                                        <div>৩য় কপি</div>
                                                    </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">
                                            <p>
                                                <span
                                                class="width_190"
                                                style="
                                                    display: inline-block;
                                                    border-bottom: 1px dotted #000;
                                                "
                                                ></span>
                                                ব্যাংকেরঃ
                                            </p>
                                            </td>
                                            <td class="text-center">
                                            <p>
                                                <span
                                                class="width_190"
                                                style="
                                                    display: inline-block;
                                                    border-bottom: 1px dotted #000;
                                                "
                                                >
                                                </span>
                                                <span>জেলার</span>
                                                <span
                                                class="width_190"
                                                style="
                                                    display: inline-block;
                                                    border-bottom: 1px dotted #000;
                                                "
                                                >
                                                </span>
                                            </p>
                                            </td>
                                            <td>
                                            <p>শাখায় টাকা জমা দেওয়ার চালান</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                            <div
                                                class="table-responsive col-xs-12 col-md-12 padding0"
                                            >
                                                <table
                                                class="
                                                    table table-bordered table-hover
                                                    text-center
                                                    margin-bottom-10
                                                "
                                                >
                                                <tbody>
                                                    <tr>
                                                    <td>যে প্রতিষ্ঠানের অনুকূলে অর্থ জমা হচ্ছে</td>
                                                    <td colspan="16">প্রাপ্তি কোড</td>
                                                    <td colspan="10">চালান নং</td>
                                                    </tr>
                                                    <tr>
                                                    <td class="text-left">
                                                        বাংলাদেশ পরিসংখ্যান ব্যুরো
                                                    
                                                    </td>
                                                    <td class="width23px">১</td>
                                                    <td>-</td>
                                                    <td class="width23px">১</td>
                                                    <td class="width23px">৬</td>
                                                    <td class="width23px">৩</td>
                                                    <td class="width23px">১</td>
                                                    <td>-</td>
                                                    <td class="width23px">0</td>
                                                    <td class="width23px">0</td>
                                                    <td class="width23px">0</td>
                                                    <td class="width23px">0</td>
                                                    <td>-</td>
                                                    <td class="width23px">২</td>
                                                    <td class="width23px">৩</td>
                                                    <td class="width23px">২</td>
                                                    <td class="width23px">১</td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    <td class="width23px"></td>
                                                    </tr>
                                                </tbody>
                                                </table>
                                            </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    <div class="table-responsive col-xs-12 padding0">
                                        <table class="table table-bordered margin-bottom-5">
                                        <thead>
                                            <tr>
                                            <th colspan="6" class="bg-gray text-center font-size15">
                                                জমা প্রদানকারী কর্তৃক পূরণ করিতে হইবে
                                            </th>
                                            </tr>
                                            <tr>
                                            <th
                                                class="text-center"
                                                rowspan="2"
                                                width="18%"
                                                style="font-weight: normal"
                                            >
                                                যাহার মারফত প্রদত্ত হইল তাহার নাম ও ঠিকানা।
                                            </th>
                                            <th
                                                class="text-center"
                                                rowspan="2"
                                                width="18%"
                                                style="font-weight: normal"
                                            >
                                                যে ব্যক্তি/প্রতিষ্ঠানের পক্ষ হইতে টাকা প্রদত্ত হইল তাহার
                                                নাম, পদবী ও ঠিকানা।
                                            </th>
                                            <th
                                                class="text-center"
                                                rowspan="2"
                                                width="15%"
                                                style="font-weight: normal"
                                            >
                                                অর্থ জমা প্রদানের মাধ্যম <br />
                                                <!-- class="print-not-showing"-->
                                                <small
                                                >(মুদ্রা ও নোটের বিবরণ/ড্রাফট, পে-অর্ডার ও চেকের
                                                বিবরণ)
                                                </small>
                                            </th>
                                            <th
                                                class="text-center"
                                                rowspan="2"
                                                width="24%"
                                                style="font-weight: normal"
                                            >
                                                কি বাবদ জমা দেওয়া হইল তাহার বিবরণ
                                            </th>
                                            <th class="text-center" colspan="2">
                                                টাকার অংক<!--জমার পরিমাণ-->
                                            </th>
                                            </tr>
                                            <tr>
                                            <th
                                                class="text-center"
                                                width="10%"
                                                style="font-weight: normal"
                                            >
                                                টাকা
                                            </th>
                                            <th
                                                class="text-center"
                                                width="6%"
                                                style="font-weight: normal"
                                            >
                                                পয়সা
                                            </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="height_140px font-size11_p">
                                                <td rowspan="4">'.
                                                    ucfirst($application->user->first_name).' '.ucfirst($application->user->middle_name).' '.ucfirst($application->user->last_name)
                                                .'</td>
                                                <td rowspan="4">
                                                '.ucfirst($application->user->first_name).' '.ucfirst($application->user->middle_name).' '.ucfirst($application->user->last_name).' <br>
                                                    '. $type.' <br>
                                                    '.ucfirst($application->user->present_address).'
                                                </td>
                                                <td rowspan="4"></td>
                                                <td class="text-left" colspan="3" style="padding: 2px">
                                                    <table
                                                    class="
                                                        table table-bordered
                                                        margin-bottom-0
                                                        coa_info_list
                                                    "
                                                    >
                                                        <tbody>
                                                            
                                                            <tr>
                                                            <td class="width40px font-size13">'. $pItem .'</td>
        
                                                            <td
                                                                class="
                                                                text-center
                                                                width25p
                                                                font-family-boishkhi
                                                                "
                                                            >
                                                                '. $price .'
                                                            </td>
                                                            <td class="text-center font-family-boishkhi">
                                                                00
                                                            </td>
                                                            </tr>
                                                            
                                                            
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-right">মোট (অংকে)=</td>
                                                <td class="text-center font-family-boishkhi">'.$application->total_price.' </td>
                                                <td class="text-center font-family-boishkhi">00</td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">ছাড় (অংকে)=</td>
                                                <td class="text-center font-family-boishkhi">'.$application->discount.' </td>
                                                <td class="text-center font-family-boishkhi">00</td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">সর্বমোট (অংকে)=</td>
                                                <td class="text-center font-family-boishkhi">'.$application->final_total.' </td>
                                                <td class="text-center font-family-boishkhi">00</td>
                                            </tr>
                                            <tr>
                                            <td colspan="6" class="text-capitalize">
                                                <p class="margin-bottom-5">
                                                টাকা (কথায়):
                                                <span
                                                    id="amountInWord"
                                                    style="
                                                    width: 89%;
                                                    display: inline-block;
                                                    border-bottom: 1px dotted #000;
                                                    "
                                                    ></span
                                                >
                                                </p>
                                                
                                            </td>
                                            </tr>
                                        </tbody>
                                        </table>
                                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">বর্ণিত অর্থ বুঝে পেলাম</td>
                                </tr>
                                <tr>
                                    <td>
                                    <table class="full-width margin-bottom-20">
                                        <tbody>
                                        <tr>
                                            <td class="width40p">
                                            <table class="table table-bordered margin-bottom-0">
                                                <tbody>
                                                <tr>
                                                    <th
                                                    class="
                                                        bg-gray
                                                        text-center
                                                        font-size15 font-normal
                                                    "
                                                    style="border-color: #000"
                                                    >
                                                    জমা গ্রহণকারী ব্যাংক কর্তৃক পূরণ করতে হবে
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                    <p class="margin-top5">
                                                        চালান নং:
                                                        <span
                                                        style="
                                                            width: 80%;
                                                            display: inline-block;
                                                            border-bottom: 1px dotted #000;
                                                        "
                                                        >
                                                        </span>
                                                    </p>
                                                    <div class="margin-bottom-5">
                                                        জমা প্রাপ্তির তারিখ:
                                                        <span
                                                        class="font-family-boishkhi width_67p"
                                                        style="
                                                            display: inline-block;
                                                            border-bottom: 1px dotted #000;
                                                        "
                                                        >
                                                        </span>
                                                    </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            </td>
                                            <td style="vertical-align: bottom">
                                            
                                            </td>
                                            <td
                                            class="width30px text-center"
                                            style="vertical-align: bottom"
                                            >
                                            <div class="sign_section"></div>
                                            <p
                                                class="margin-bottom-0 font-size15"
                                                style="border-top: 1px solid"
                                            >
                                                ম্যানেজার
                                            </p>
                                            <div class="margin-bottom-0" style="float: right;">
                                                বাংলাদেশ ব্যাংক / সোনালী ব্যাংক লিমিটেড
                                            </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <br /><br />
                
                            <div id="printChallanSecondCopy" class="hide">
                                <hr style="border: 1px dashed" />
                                <table class="col-xs-12 multi-challan-content padding0 clearfix">
                                <tbody>
                                    <tr>
                                    
                                    </tr>
                                    <tr>
                                    <td>
                                        <div class="table-responsive col-xs-12 padding0">
                                        <table class="table table-bordered margin-bottom-5">
                                            <thead>
                                            <tr>
                                                <th colspan="6" class="bg-gray text-center font-size15">
                                                জমা প্রদানকারী কর্তৃক পূরণ করিতে হইবে
                                                </th>
                                            </tr>
                                            <tr>
                                                <th
                                                class="text-center"
                                                rowspan="2"
                                                width="18%"
                                                style="font-weight: normal"
                                                >
                                                যাহার মারফত প্রদত্ত হইল তাহার নাম ও ঠিকানা।
                                                </th>
                                                <th
                                                class="text-center"
                                                rowspan="2"
                                                width="18%"
                                                style="font-weight: normal"
                                                >
                                                যে ব্যক্তি/প্রতিষ্ঠানের পক্ষ হইতে টাকা প্রদত্ত হইল
                                                তাহার নাম, পদবী ও ঠিকানা।
                                                </th>
                                                <th
                                                class="text-center"
                                                rowspan="2"
                                                width="15%"
                                                style="font-weight: normal"
                                                >
                                                অর্থ জমা প্রদানের মাধ্যম <br />
                                                <!-- class="print-not-showing"-->
                                                <small
                                                    >(মুদ্রা ও নোটের বিবরণ/ড্রাফট, পে-অর্ডার ও চেকের
                                                    বিবরণ)
                                                </small>
                                                </th>
                                                <th
                                                class="text-center"
                                                rowspan="2"
                                                width="24%"
                                                style="font-weight: normal"
                                                >
                                                কি বাবদ জমা দেওয়া হইল তাহার বিবরণ
                                                </th>
                                                <th class="text-center" colspan="2">
                                                টাকার অংক
                                                <!--জমার পরিমাণ-->
                                                </th>
                                            </tr>
                                            <tr>
                                                <th
                                                class="text-center"
                                                width="10%"
                                                style="font-weight: normal"
                                                >
                                                টাকা
                                                </th>
                                                <th
                                                class="text-center"
                                                width="6%"
                                                style="font-weight: normal"
                                                >
                                                পয়সা
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr class="height_140px font-size11_p">
                                                <td rowspan="2">
                                                Amber Arnold, 01518643843, Veniam ipsum rem a
                                                </td>
                                                <td rowspan="2">
                                                Amber Arnold, 01518643843, Veniam ipsum rem a
                                                </td>
                                                <td rowspan="2"></td>
                                                <td class="text-left" colspan="3" style="padding: 2px">
                                                <table
                                                    class="
                                                    table table-bordered
                                                    margin-bottom-0
                                                    coa_info_list
                                                    "
                                                >
                                                    <tbody>
                                                    <tr>
                                                        <td class="width40p font-size13">
                                                        পাসপোর্ট ফি
                                                        </td>
                                                        <td class="width23px">১</td>
                                                        <td class="width23px">৮</td>
                                                        <td class="width23px">৪</td>
                                                        <td class="width23px">৬</td>
                                                        <td
                                                        class="
                                                            text-center
                                                            width25p
                                                            font-family-boishkhi
                                                        "
                                                        >
                                                        0
                                                        </td>
                                                        <td class="text-center font-family-boishkhi">
                                                        00
                                                        </td>
                                                    </tr>
                
                                                    <tr>
                                                        <td class="width40p font-size13">
                                                        দেশজ পণ্য ও সেবার উপর ভ্যাট
                                                        </td>
                                                        <td class="width23px">০</td>
                                                        <td class="width23px">৩</td>
                                                        <td class="width23px">১</td>
                                                        <td class="width23px">১</td>
                                                        <td
                                                        class="
                                                            text-center
                                                            width25p
                                                            font-family-boishkhi
                                                        "
                                                        >
                                                        450
                                                        </td>
                                                        <td class="text-center font-family-boishkhi">
                                                        00
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right">মোট (অংকে)=</td>
                                                <td class="text-center font-family-boishkhi">450</td>
                                                <td class="text-center font-family-boishkhi">00</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="text-capitalize">
                                                <p class="margin-bottom-5">
                                                    টাকা (কথায়):
                                                    <span
                                                    id="amountInWord_bottom"
                                                    style="
                                                        width: 89%;
                                                        display: inline-block;
                                                        border-bottom: 1px dotted #000;
                                                    "
                                                    >চার শত পঞ্চাশ টাকা মাত্র</span
                                                    >
                                                </p>
                                                <script>
                                                    var amount = 450;
                                                    amount = amount.toFixed(2);
                                                    var ECO_CODE_AMOUNT = convertToBanglaWords(amount);
                                                    document.getElementById(
                                                    "amountInWord_bottom"
                                                    ).innerHTML = "" + ECO_CODE_AMOUNT;
                                                </script>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td class="text-right">বর্ণিত অর্থ বুঝে পেলাম</td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table class="full-width margin-bottom-0">
                                        <tbody>
                                            <tr>
                                            <td class="width40p">
                                                <table class="table table-bordered margin-bottom-0">
                                                <tbody>
                                                    <tr>
                                                    <th
                                                        class="
                                                        bg-gray
                                                        text-center
                                                        font-size15 font-normal
                                                        "
                                                        style="border-color: #000"
                                                    >
                                                        জমা গ্রহণকারী ব্যাংক কর্তৃক পূরণ করতে হবে
                                                    </th>
                                                    </tr>
                                                    <tr>
                                                    <td>
                                                        <p class="margin-top5">
                                                        চালান নং:
                                                        <span
                                                            style="
                                                            width: 80%;
                                                            display: inline-block;
                                                            border-bottom: 1px dotted #000;
                                                            "
                                                        >
                                                        </span>
                                                        </p>
                                                        <div class="margin-bottom-5">
                                                        জমা প্রাপ্তির তারিখ:
                                                        <span
                                                            class="font-family-boishkhi width_67p"
                                                            style="
                                                            display: inline-block;
                                                            border-bottom: 1px dotted #000;
                                                            "
                                                        >
                                                        </span>
                                                        </div>
                                                    </td>
                                                    </tr>
                                                </tbody>
                                                </table>
                                            </td>
                                            <td style="vertical-align: bottom">
                                                <!--<div class="sill-box">
                                                                                    সিল
                                                                                </div>-->
                                            </td>
                                            <td
                                                class="width30p text-center"
                                                style="vertical-align: bottom"
                                            >
                                                <div class="sign_section"></div>
                                                <p
                                                class="margin-bottom-0 font-size15"
                                                style="border-top: 1px solid"
                                                >
                                                ম্যানেজার
                                                </p>
                                                <div class="margin-bottom-0">
                                                বাংলাদেশ ব্যাংক / সোনালী ব্যাংক লিমিটেড
                                                </div>
                                            </td>
                                            </tr>
                                        </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                            </div>
                
                            <div id="editor"></div>
                
                            <div class="margin-tb-10">
                            
                            </div>
                        </div>
                    </div>
                </div>';


        $mpdf = new \Mpdf\Mpdf([
            'default_font_size' => 12,
            'default_font'      => 'kalpurush'
        ]);
    

        $mpdf->WriteHtml($div);
        return $mpdf->Output();
    }

    public function paymentApprove(Application $application, Request $request)
    {

        // dd($request->type);
        if($request->type == "approve")
        {
            if($pay = $application->payments)
            {   
                $application->is_paid = true;

                $pay->is_app = true;

                $applicationServices = ApplicationService::with('serviceItem')->where('application_id', $application->id)->get();
                // dd($applicationServices);
                foreach($applicationServices as $item)
                {
                    // dd($item->service_inventory_item_id);
                    // generate unique link
                    $currentDatetime = date('Ymdhis');
                    $str = rand();
                    $mdstr = md5($str);
                    $link = $currentDatetime . $mdstr;

                    // Generate unique download token for service items
                    $str2 = rand();
                    $mdstr2 = md5($str2);
                    $downloadToken = $currentDatetime . $mdstr2;

                    $appServiceItemDownload = new ApplicationServiceItemDownload;
                    $appServiceItemDownload->application_id = $item->application_id;
                    $appServiceItemDownload->service_id = $item->service_id;
                    $appServiceItemDownload->service_inventory_item_id = $item->service_inventory_item_id; // for service inventory item

                    $appServiceItemDownload->service_item_id = $item->serviceItem->id;
                    $appServiceItemDownload->file_path = "storage/service/item/". $item->serviceItem->attachment;
                    $appServiceItemDownload->link = $link;
                    $appServiceItemDownload->download_token = $downloadToken;
                    $appServiceItemDownload->total_download = 0;
                    $appServiceItemDownload->save();

                    if($appServiceItemDownload->service_id == 3)
                    {
                        $template = TemplateSetting::where('service_item_id',$appServiceItemDownload->service_item_id)->first();

                        if($template)
                        {
                            $certificate = new Certificate;
                        
                            $certificate->application_id = $appServiceItemDownload->application_id;
                            $certificate->sr_user_id = $appServiceItemDownload->application->sr_user_id;
                            $certificate->service_item_id = $appServiceItemDownload->service_item_id;

                            // $certificate->certificate_no = 
                            // $certificate->certificate_date = 
                            $certificate->content = $template->body;
                            $certificate->template_id = $template->id;
                            // $certificate->office_id = 
                            // $certificate->level_id = 
                            // $certificate->division_id = $appServiceItemDownload->application->division_id;
                            // $certificate->district_id = $appServiceItemDownload->application->district_id;
                            // $certificate->upazila_id = $appServiceItemDownload->application->upazila_id;
                            $certificate->created_by = Auth::id();
                            $certificate->created_by_signature = Auth::user()->signature;
                            $certificate->created_by_designation = Auth::user()->designation_id; 
                            // $certificate->modified = 
                            $certificate->status = true;
                            $certificate->save();
                        }
                        else
                        {
                            return back()->with('info','No Template Found for this certificate service.');
                        }
                    }

                }

                $pay->save();
                $application->save();

                // Email Datas
                $user = $application->user;
                $downloadTokens = ApplicationServiceItemDownload::where('application_id', $application->id)->get();

                // Email with service items download tokens
                Mail::to($user->email)->send(new DownloadTokens($user, $downloadTokens, $application));

                return back()->with('success','Approve Successfully.');
            }
            else 
            {
                return back()->with('info','Someting Went To Wrong.');
                
            }
        }
        elseif($request->type == 'cancel') 
        {

            if($pay = $application->payment)
            {
                $pay->is_app = false;
                $application->is_paid = 2;

                $pay->save();
                $application->save();
                return back()->with('success','Cancel Successfully.');
            }
            else 
            {
                return back()->with('info','Someting Went To Wrong.');
                
            }

        }
    }

     /**
     * Application service items download method
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadlinks($application_id)
    {
       
        $applicationServices = ApplicationServiceItemDownload::with('service', 'serviceItem')->where('application_id', $application_id)->get();
        $serviceId = $applicationServices[0]->service_id;
        if($serviceId == null)
        {
            $serviceId = 1;
        }
        return view('backend.serviceRecipient.application.downloadItems', compact('applicationServices','serviceId'));
    }

     /**
     * Application service items download with unique link
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadItem(Request $request, $link)
    {
        // dd($link);
        $appServiceItemDownload = ApplicationServiceItemDownload::where('link', $link)->first();
        // dd($appServiceItemDownload);
        
        if($request->download_token == $appServiceItemDownload->download_token)
        {

            $appServiceItemDownloadDetail = new ApplicationServiceItemDownloadDetail;
            $appServiceItemDownloadDetail->ip_address = $request->ip();
            $appServiceItemDownloadDetail->user_id = Auth::user()->id;
            $appServiceItemDownloadDetail->application_id = $appServiceItemDownload->application_id;
            $appServiceItemDownloadDetail->application_service_item_download_id = $appServiceItemDownload->id;
            $appServiceItemDownloadDetail->download_quantity = 1;
            $appServiceItemDownloadDetail->save();
    
            $totalDownload = ApplicationServiceItemDownloadDetail::where('application_service_item_download_id', $appServiceItemDownload->id)->count();
            $appServiceItemDownload->total_download = $totalDownload;
            $appServiceItemDownload->save();
            
            $file_path = $appServiceItemDownload->file_path;

            $exist = File::exists($file_path);
            if($exist){

                return response()->download(public_path($file_path));
            }else{
                return back()->with('error', 'File not exist');
            }

            
    
        }
        else
        {
            return back()->with('error','Your Given Token Is Not Valid. Please Try With Valid Token.');
        }

    }

    public function invoice(Application $application)
    {
        $auth = Auth::user();

        return view('backend.serviceRecipient.application.invoice',[
            'order' => $application,
            'auth'=>$auth
        ]);
    }

    public function certificatePreview(Request $request)
    {
        $applicationId = $request->application;
        $serviceItem = $request->serviceItem;
        $certificate = Certificate::where('application_id',$applicationId)->where('service_item_id',$serviceItem)->first();
        if($certificate)
        {
            $template = TemplateSetting::where('id',$certificate->template_id)->first();
        
            $applicationService = ApplicationService::where('application_id',$certificate->application_id)->where('service_item_id',$certificate->service_item_id)->first();
            $application = $applicationService->application;
        }
        else
        {
            $template =[];
            $applicationService = ApplicationService::where('application_id',$applicationId)->where('service_item_id',$serviceItem)->first();
            $application = $applicationService->application;
        }
        
        return view('backend.serviceRecipient.application.certificatePreview',[
            'application'=>$application,
            'template' =>$template,
            'applicationService' =>$applicationService,
            'certificate' => $certificate
        ]);
    }

    public function changeCertificate(Certificate $certificate, Request $request)
    {
        // dd($request->all());
        $certificate->content   = $request->body;
        $certificate->save();

        
        if($request->hasFile('files'))
        {
            $cp = $request->file('files');
            $extension = strtolower($cp->getClientOriginalExtension());
            $randomFileName = $certificate->id.'file'.date('Y_m_d_his').'_'.rand(10000000,99999999).'.'.$extension;

            Storage::disk('public')->put('certificate/files/'.$randomFileName, File::get($cp));

            $certificate->attach_files = $randomFileName;
            $certificate->save();
      	} 

        return back()->with('success','Successfully Change'); 
    }
    
    
    // still have work in service inventory minius
    public function courierToken(Application $application,Request $request)
    {
       if ($request->token) {
            $application->courier_token = $request->token;
            $application->save();
            $serviceInverntoryItem = $request->service_inventory_item_id;
        
            $data = ApplicationServiceItemDownload::where('application_id',$application->id)->where('service_inventory_item_id',$serviceInverntoryItem)->first();
            $data->is_deliver = true;
            $data->save();

            $serviceInventoryData = ServiceInventory::find($serviceInverntoryItem);
            $serviceInventoryData->number_of_sale_copies = $serviceInventoryData->number_of_sale_copies - 1;
            $serviceInventoryData->save();
            
            return back()->with('success','Successfully Send Token To Recipient.');
       }
       else{
        return back()->with('error','Token can not be empty.');

       }
        

    }

    public function productDelivery(Application $application,Request $request)
    {
        $serviceInverntoryItem = $request->service_inventory_item_id;
        
        $data = ApplicationServiceItemDownload::where('application_id',$application->id)->where('service_inventory_item_id',$serviceInverntoryItem)->first();
        $data->is_deliver = true;
        $data->save();

        $serviceInventoryData = ServiceInventory::find($serviceInverntoryItem);
        $serviceInventoryData->number_of_sale_copies = $serviceInventoryData->number_of_sale_copies - 1;
        $serviceInventoryData->save();
        return back()->with('success','Successfully Hand Over To Recipient.');

    }
    
}
