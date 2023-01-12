<?php

namespace App\Http\Controllers\Admin;


use App\Models\Message;
use App\Models\Result;

use Illuminate\Http\Request;

use App\Http\Requests\MessageRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use Alert;

/**
 * Class MessageCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MessageCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Message::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/message');
        CRUD::setEntityNameStrings('message', 'messages');

        
        $this->crud->allowAccess('send-sms-to-worker');
        $this->crud->addButtonFromModelFunction('line', 'send-sms', 'sendSms', 'beginning');

        $this->crud->addFilter([
            'type'  => 'date_range',
            'name'  => 'created_at',
            'label' => 'Created_at'
          ],
          false,
          function ($value) { // if the filter is active, apply these constraints
              $dates = json_decode($value);
              $this->crud->addClause('where', 'created_at', '>=', $dates->from);
              $this->crud->addClause('where', 'created_at', '<=', $dates->to . ' 23:59:59');
          });

          $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'status',
            'label' => 'Status Message'
        ],
            [
                1 => __('Sending'),
                0 => __('Not sending')
            ],
            function ($value) {
                if ($value == 2)
                    $this->crud->query = Message::select('*');
                else
                    $this->crud->addClause('where', 'status_message', $value);
            });

    }


    public function getSendSmsToWorker($id)
    {
        $worker = Message::find($id);
        $results = Result::all();

        return view('backpack::send-sms-to-worker', [
            'worker' => $worker,
            'id' => $id,
            'results' => $results
        ]);
    }


    public function postSendSmsToWorker(Request $request)
    {
        Alert::success('Successfully send')->flash();
        return redirect()
            ->route('message.index')
            ->with([
                'status' => 'success',
                'message' => 'successfully sanded'
            ]);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        $this->crud->addColumn([
            'name' => 'id',
            'label' => '№'
        ]);

        $this->crud->addColumn([
            'name' => 'member',
            'label' => 'Phone',
            'type' => 'model_function',
            'function_name' => 'phone'
        ]);

        $this->crud->addColumn([
            'name' => 'fullname',
            'label' => 'Worker'
        ]);

        $this->crud->addColumn([
            'name' => 'comment',
            'label' => 'Comment'
        ]);

        $this->crud->addColumn([
            'name' => 'comment_result',
            'label' => 'Result'
        ]);

        
        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => 'Created_at'
        ]);

        $this->crud->addColumn([
            'name' => 'status_message',
            'label' => 'Status',
            'type' => 'view',
            'view' => 'backpack::crud.status',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MessageRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
