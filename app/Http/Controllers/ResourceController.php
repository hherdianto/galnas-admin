<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Models;
use Illuminate\Http\Response;
use DB;
use App\Services\ResourceService;
use App\Services\FormService;
use App\Models\Form;
use App\Models\FormField;
use Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Throwable;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $table
     * @param Request $request
     * @return Response | View
     */
    public function index($table, Request $request)
    {
        $guestHasPermission = false;
        $role = Role::where('name', '=', 'guest')->first();
        try {
            if($role->hasPermissionTo('browse bread ' . $table)){
                $guestHasPermission = true;
            }
        } catch (Throwable $e) {
            $guestHasPermission = false;
        }
        if(!$guestHasPermission){
            if(empty(Auth::user())){
                abort('401');
            }else{
                if(!Auth::user()->can('browse bread ' . $table)){
                    abort('401');
                }
            }
        }
        $form = Form::find( $table );
        $resourceService = new ResourceService();
        $data = $resourceService->getIndexDatas( $table );
        return view('dashboard.resource.index', [
            'form' => $form,
            'header' => $resourceService->getFullIndexHeader( $table ),
            'datas' => $data['data'],
            'pagination' => $data['pagination'],
            'enableButtons' => $data['enableButtons']
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param $table
     * @param Request $request
     * @return Response | View
     */
    public function create($table, Request $request)
    {
        $guestHasPermission = false;
        $role = Role::where('name', '=', 'guest')->first();
        try {
            if($role->hasPermissionTo('add bread ' . $table)){
                $guestHasPermission = true;
            }
        } catch (Throwable $e) {
            $guestHasPermission = false;
        }
        if(!$guestHasPermission){
            if(empty(Auth::user())){
                abort('401');
            }else{
                if(!Auth::user()->can('add bread ' . $table)){
                    abort('401');
                }
            }
        }
        $form = Form::find( $table );
        if($form->add == 1){
            $resourceService = new ResourceService();
            $formService = new FormService();
            $columns = $resourceService->getColumnsForAdd( $table );

            return view('dashboard.resource.create', [
                'form' => $form,
                'columns' => $columns,
                'relations' => $resourceService->getRelations( $columns ),
                'inputOptions' => $formService->getFromOptionsStandardInput(),
            ]);
        }else{
            abort('401');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $table
     * @param Request $request
     * @return Response | RedirectResponse
     */
    public function store($table, Request $request)
    {
        $guestHasPermission = false;
        $role = Role::where('name', '=', 'guest')->first();
        try {
            if($role->hasPermissionTo('add bread ' . $table)){
                $guestHasPermission = true;
            }
        } catch (Throwable $e) {
            $guestHasPermission = false;
        }
        if(!$guestHasPermission){
            if(empty(Auth::user())){
                abort('401');
            }else{
                if(!Auth::user()->can('add bread ' . $table)){
                    abort('401');
                }
            }
        }
        $toValidate = array();
        $form = Form::find( $table );
        $formFields = FormField::where('form_id', '=', $table)->where('add', '=', '1')->get();
        foreach($formFields as $formField){
            $toValidate[$formField->column_name] = 'required';
        }
        $request->validate($toValidate);
        if($form->add == 1){
            $resourceService = new ResourceService();
            $resourceService->add($form->id, $form->table_name, $request->all() );
            $request->session()->flash('message', 'Successfully added to ' . $form->name);
            return redirect()->route('resource.index', $table );
        }else{
            abort('401');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $table
     * @param int $id
     * @param Request $request
     * @return Response | View
     */
    public function show($table, $id, Request $request)
    {
        $guestHasPermission = false;
        $role = Role::where('name', '=', 'guest')->first();
        try {
            if($role->hasPermissionTo('read bread ' . $table)){
                $guestHasPermission = true;
            }
        } catch (Throwable $e) {
            $guestHasPermission = false;
        }
        if(!$guestHasPermission){
            if(empty(Auth::user())){
                abort('401');
            }else{
                if(!Auth::user()->can('read bread ' . $table)){
                    abort('401');
                }
            }
        }
        $form = Form::find( $table );
        if($form->read == 1){
            $resourceService = new ResourceService();
            return view('dashboard.resource.show', [
                'form' => $form,
                'columns' => $resourceService->show($form->id, $form->table_name, $id),
            ]);
        }else{
            abort('401');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $table
     * @param int $id
     * @return Response | View
     */
    public function edit($table, $id)
    {
        $guestHasPermission = false;
        $role = Role::where('name', '=', 'guest')->first();
        try {
            if($role->hasPermissionTo('edit bread ' . $table)){
                $guestHasPermission = true;
            }
        } catch (Throwable $e) {
            $guestHasPermission = false;
        }
        if(!$guestHasPermission){
            if(empty(Auth::user())){
                abort('401');
            }else{
                if(!Auth::user()->can('edit bread ' . $table)){
                    abort('401');
                }
            }
        }
        $form = Form::find( $table );
        if($form->edit == 1){
            $resourceService = new ResourceService();
            $formService = new FormService();
            return view('dashboard.resource.edit', [
                'form' => $form,
                'columns' => $resourceService->getColumnsForEdit( $form->table_name, $table, $id ),
                'relations' => $resourceService->getRelations( FormField::where('form_id', '=', $table)->where('edit', '=', '1')->get()),
                'inputOptions' => $formService->getFromOptionsStandardInput(),
                'id' => $id,
            ]);
        }else{
            abort('401');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $table
     * @param int $id
     * @param Request $request
     * @return Response | RedirectResponse
     */
    public function update($table, $id, Request $request)
    {
        $guestHasPermission = false;
        $role = Role::where('name', '=', 'guest')->first();
        try {
            if($role->hasPermissionTo('edit bread ' . $table)){
                $guestHasPermission = true;
            }
        } catch (Throwable $e) {
            $guestHasPermission = false;
        }
        if(!$guestHasPermission){
            if(empty(Auth::user())){
                abort('401');
            }else{
                if(!Auth::user()->can('edit bread ' . $table)){
                    abort('401');
                }
            }
        }
        $toValidate = array();
        $form = Form::find( $table );
        $formFields = FormField::where('form_id', '=', $table)->where('add', '=', '1')->get();
        foreach($formFields as $formField){
            $toValidate[$formField->column_name] = 'required';
        }
        $request->validate($toValidate);
        if($form->edit == 1){
            $resourceService = new ResourceService();
            $resourceService->update($form->table_name, $table, $id, $request->all() );
            $request->session()->flash('message', 'Successfully edited ' . $form->name);
            return redirect()->route('resource.index', $table );
        }else{
            abort('401');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $table
     * @param Request $request
     * @param int $id
     * @return Response | RedirectResponse | View
     */
    public function destroy($table, Request $request, $id)
    {
        $guestHasPermission = false;
        $role = Role::where('name', '=', 'guest')->first();
        try {
            if($role->hasPermissionTo('delete bread ' . $table)){
                $guestHasPermission = true;
            }
        } catch (Throwable $e) {
            $guestHasPermission = false;
        }
        if(!$guestHasPermission){
            if(empty(Auth::user())){
                abort('401');
            }else{
                if(!Auth::user()->can('delete bread ' . $table)){
                    abort('401');
                }
            }
        }
        $form = Form::find( $table );
        if($form->delete == 1){
            if($request->has('marker')){
                DB::table($form->table_name)->where('id', '=', $id)->delete();
                $request->session()->flash('message', 'Successfully deleted element from: ' . $form->name);
                return redirect()->route('resource.index', $table);
            }else{
                return view('dashboard.resource.delete', ['table' => $table, 'id' => $id, 'formName' => $form->name]);
            }
        }else{
            abort('401');
        }
    }
}
