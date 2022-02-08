<?php


namespace App\Http\Controllers;


use App\Models\AppConfig;
use Illuminate\Http\Request;

class AppConfigController extends Controller
{
    public function index() {
        $configs = AppConfig::all();
        return view('dashboard.configs.list')->with(['configs' => $configs]);
    }

    public function edit($id) {
        $config = AppConfig::findOrFail($id);
        return view('dashboard.configs.edit')->with(['config' => $config]);
    }

    public function update(Request $request, $id) {
        $config = AppConfig::findOrFail($id);
        $config->value = $request->value;
        $config->notes = $request->notes;
        $config->save();
        return redirect()->route('configs');
    }
}
