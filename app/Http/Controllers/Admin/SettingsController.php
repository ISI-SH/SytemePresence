<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'fixed_arrival_time' => Config::get('attendance.fixed_arrival_time', '09:00'),
            'late_tolerance_minutes' => Config::get('attendance.late_tolerance_minutes', 0),
            'work_start_time' => Config::get('attendance.work_start_time', '08:00'),
            'work_end_time' => Config::get('attendance.work_end_time', '17:00'),
            'required_daily_hours' => Config::get('attendance.required_daily_hours', 8),
        ];
        
        return view('admin.settings.index', compact('settings'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'fixed_arrival_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0|max:60',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'required_daily_hours' => 'required|integer|min:1|max:12',
        ]);
        
        $configPath = config_path('attendance.php');
        $config = include $configPath;
        
        $config['fixed_arrival_time'] = $request->fixed_arrival_time;
        $config['late_tolerance_minutes'] = $request->late_tolerance_minutes;
        $config['work_start_time'] = $request->work_start_time;
        $config['work_end_time'] = $request->work_end_time;
        $config['required_daily_hours'] = $request->required_daily_hours;
        
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents($configPath, $content);
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètres de présence mis à jour avec succès.');
    }
}
