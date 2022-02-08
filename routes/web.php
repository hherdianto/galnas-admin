<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\EventSchedule;

Route::group(['middleware' => ['get.menu']], function () {
    Route::get('/', function () {
//        return view('dashboard.homepage');
        if (auth()->check())
            return redirect('/dashboard');
        else
            return redirect('/login');
    });
    Route::get('test', function () {
        dd(EventSchedule::whereDate('start_time', '<=', now()->addWeek())
            ->where(['event_id' => 1])->orderByRaw('DATE(start_time)', 'desc')
            ->selectRaw('DATE(start_time)')->groupByRaw('DATE(start_time)')->limit(7)->get());
    });
    Auth::routes();

    Route::group(['middleware' => ['auth']], function () {
//        Route::group(['middleware' => ['role:user|Operator']], function () {
        Route::group(['middleware' => ['role:Frontdesk']], function () {
            Route::get('/colors', function () {
                return view('dashboard.colors');
            });
            Route::get('/typography', function () {
                return view('dashboard.typography');
            });
            Route::get('/charts', function () {
                return view('dashboard.charts');
            });
            Route::get('/widgets', function () {
                return view('dashboard.widgets');
            });
            Route::get('/404', function () {
                return view('dashboard.404');
            });
            Route::get('/500', function () {
                return view('dashboard.500');
            });
            Route::prefix('dashboard')->group(function () {
                Route::get('/', 'DashboardController@index')->name('dashboard');
                Route::get('/onlinePerDay', 'DashboardController@onlinePerDay')->name('dashboard.onlinePerDay');
                Route::get('/offlinePerDay', 'DashboardController@offlinePerDay')->name('dashboard.offlinePerDay');
                Route::get('/confirmedPerDay', 'DashboardController@confirmedPerDay')->name('dashboard.confirmedPerDay');
                Route::get('/registerPerDay', 'DashboardController@registerPerDay')->name('dashboard.registerPerDay');
                Route::get('/visitPerEvent', 'DashboardController@visitPerEvent')->name('dashboard.visitPerEvent');
            });
            Route::prefix('base')->group(function () {
                Route::get('/breadcrumb', function () {
                    return view('dashboard.base.breadcrumb');
                });
                Route::get('/cards', function () {
                    return view('dashboard.base.cards');
                });
                Route::get('/carousel', function () {
                    return view('dashboard.base.carousel');
                });
                Route::get('/collapse', function () {
                    return view('dashboard.base.collapse');
                });

                Route::get('/forms', function () {
                    return view('dashboard.base.forms');
                });
                Route::get('/jumbotron', function () {
                    return view('dashboard.base.jumbotron');
                });
                Route::get('/list-group', function () {
                    return view('dashboard.base.list-group');
                });
                Route::get('/navs', function () {
                    return view('dashboard.base.navs');
                });

                Route::get('/pagination', function () {
                    return view('dashboard.base.pagination');
                });
                Route::get('/popovers', function () {
                    return view('dashboard.base.popovers');
                });
                Route::get('/progress', function () {
                    return view('dashboard.base.progress');
                });
                Route::get('/scrollspy', function () {
                    return view('dashboard.base.scrollspy');
                });

                Route::get('/switches', function () {
                    return view('dashboard.base.switches');
                });
                Route::get('/tables', function () {
                    return view('dashboard.base.tables');
                });
                Route::get('/tabs', function () {
                    return view('dashboard.base.tabs');
                });
                Route::get('/tooltips', function () {
                    return view('dashboard.base.tooltips');
                });
            });
            Route::prefix('buttons')->group(function () {
                Route::get('/buttons', function () {
                    return view('dashboard.buttons.buttons');
                });
                Route::get('/button-group', function () {
                    return view('dashboard.buttons.button-group');
                });
                Route::get('/dropdowns', function () {
                    return view('dashboard.buttons.dropdowns');
                });
                Route::get('/brand-buttons', function () {
                    return view('dashboard.buttons.brand-buttons');
                });
            });
            Route::prefix('icon')->group(function () {  // word: "icons" - not working as part of adress
                Route::get('/coreui-icons', function () {
                    return view('dashboard.icons.coreui-icons');
                });
                Route::get('/flags', function () {
                    return view('dashboard.icons.flags');
                });
                Route::get('/brands', function () {
                    return view('dashboard.icons.brands');
                });
            });
            Route::prefix('notifications')->group(function () {
                Route::get('/alerts', function () {
                    return view('dashboard.notifications.alerts');
                });
                Route::get('/badge', function () {
                    return view('dashboard.notifications.badge');
                });
                Route::get('/modals', function () {
                    return view('dashboard.notifications.modals');
                });
            });
            Route::prefix('events')->group(function () {
                Route::get('/', 'EventController@index')->name('events');
                Route::get('/fetch', 'EventController@fetch')->name('events.fetch');
                Route::get('/create', [\App\Http\Controllers\EventController::class, 'create'])->name('events.create');
                Route::post('/', 'EventController@store')->name('events.store');
                Route::get('/{id}/slots', [\App\Http\Controllers\EventController::class, 'getTimeSlots'])->name('events.slots');
                Route::post('/{id}/slot', 'EventController@scheduleStore')->name('events.slot.store');
                Route::post('/{id}/addDate', 'EventController@addDate')->name('events.slot.addDate');
                Route::post('/{id}/slots', 'EventController@slotStore')->name('events.slots.store');
                Route::post('/{id}/images', 'EventController@saveImage')->name('events.image.store');
                Route::get('/{id}/edit', 'EventController@edit')->name('events.edit');
                Route::delete('/{id}/schedule', 'EventScheduleController@deactivate')->name('events.schedule.deactivate');
                Route::put('/{id}/schedule/toggle', 'EventScheduleController@toggleDate')->name('events.schedule.toggle');
                Route::put('/{id}', 'EventController@update')->name('events.update');
                Route::delete('/{id}', 'EventController@delete')->name('events.delete');

                Route::patch('/{id}/dow/{dayOfWeek}', [\App\Http\Controllers\EventController::class, 'updateDow'])->name('events.dow.store');
            });
            Route::prefix('visitors')->group(function () {
                Route::get('/', 'VisitorController@index')->name('visitors');
                Route::get('/fetch', 'VisitorController@fetch')->name('visitors.fetch');
                Route::get('/create', 'VisitorController@create')->name('visitors.create');
                Route::post('/', 'VisitorController@store')->name('visitors.store');
                Route::post('/{id}/slots', 'VisitorController@slotStore')->name('visitors.slots.store');
                Route::post('/{id}/images', 'VisitorController@saveImage')->name('visitors.image.store');
                Route::get('/{id}/edit', 'VisitorController@edit')->name('visitors.edit');
                Route::put('/{id}', 'VisitorController@update')->name('visitors.update');
                Route::delete('/{id}', 'VisitorController@delete')->name('visitors.delete');
            });
            Route::prefix('schedules')->group(function () {
                Route::get('/', 'EventScheduleController@index')->name('schedules');
                Route::get('/fetch', 'EventScheduleController@fetch')->name('schedules.fetch');
                Route::get('/create', 'EventScheduleController@create')->name('schedules.create');
                Route::post('/', 'EventScheduleController@store')->name('schedules.store');
                Route::post('/{id}/slots', 'EventScheduleController@slotStore')->name('schedules.slots.store');
                Route::post('/{id}/images', 'EventScheduleController@saveImage')->name('schedules.image.store');
                Route::get('/{id}/edit', 'EventScheduleController@edit')->name('schedules.edit');
                Route::put('/{id}', 'EventScheduleController@update')->name('schedules.update');
                Route::delete('/{id}', 'EventScheduleController@delete')->name('schedules.delete');
            });
            Route::prefix('visits')->group(function () {
                Route::get('/', 'EventVisitController@index')->name('visits');
                Route::get('/fetch', 'EventVisitController@fetch')->name('visits.fetch');
                Route::get('/create', 'EventVisitController@create')->name('visits.create');
                Route::post('/', 'EventVisitController@store')->name('visits.store');
                Route::post('/{id}/slots', 'EventVisitController@slotStore')->name('visits.slots.store');
                Route::post('/{id}/images', 'EventVisitController@saveImage')->name('visits.image.store');
                Route::post('/{id}/confirm', 'EventVisitController@confirm')->name('visits.image.confirm');
                Route::post('/{id}/remove', 'EventVisitController@remove')->name('visits.image.remove');
                Route::get('/{id}', 'EventVisitController@get')->name('visits.get');
                Route::get('/{id}/edit', 'EventVisitController@edit')->name('visits.edit');
                Route::put('/{id}', 'EventVisitController@update')->name('visits.update');
                Route::delete('/{id}', 'EventVisitController@delete')->name('visits.delete');
            });
            Route::prefix('scans')->group(function () {
                Route::get('/', 'ScanController@index')->name('scans');
                Route::post('/', 'ScanController@post')->name('scans.post');
            });
            Route::resource('notes', 'NotesController');
        });

        Route::resource('resource/{table}/resource', 'ResourceController')->names([
            'index' => 'resource.index',
            'create' => 'resource.create',
            'store' => 'resource.store',
            'show' => 'resource.show',
            'edit' => 'resource.edit',
            'update' => 'resource.update',
            'destroy' => 'resource.destroy'
        ]);

        Route::group(['middleware' => ['role:admin']], function () {
            Route::resource('bread', 'BreadController');   //create BREAD (resource)
            Route::resource('users', 'UsersController');
            Route::resource('roles', 'RolesController');
            Route::resource('mail', 'MailController');
            Route::get('prepareSend/{id}', 'MailController@prepareSend')->name('prepareSend');
            Route::post('mailSend/{id}', 'MailController@send')->name('mailSend');
            Route::get('mailTemplate/{id}', 'MailController@showTemplate')->name('mail.show');
            Route::get('/roles/move/move-up', 'RolesController@moveUp')->name('roles.up');
            Route::get('/roles/move/move-down', 'RolesController@moveDown')->name('roles.down');
            Route::prefix('menu/element')->group(function () {
                Route::get('/', 'MenuElementController@index')->name('menu.index');
                Route::get('/move-up', 'MenuElementController@moveUp')->name('menu.up');
                Route::get('/move-down', 'MenuElementController@moveDown')->name('menu.down');
                Route::get('/create', 'MenuElementController@create')->name('menu.create');
                Route::post('/store', 'MenuElementController@store')->name('menu.store');
                Route::get('/get-parents', 'MenuElementController@getParents');
                Route::get('/edit', 'MenuElementController@edit')->name('menu.edit');
                Route::post('/update', 'MenuElementController@update')->name('menu.update');
                Route::get('/show', 'MenuElementController@show')->name('menu.show');
                Route::get('/delete', 'MenuElementController@delete')->name('menu.delete');
            });
            Route::prefix('menu/menu')->group(function () {
                Route::get('/', 'MenuController@index')->name('menu.menu.index');
                Route::get('/create', 'MenuController@create')->name('menu.menu.create');
                Route::post('/store', 'MenuController@store')->name('menu.menu.store');
                Route::get('/edit', 'MenuController@edit')->name('menu.menu.edit');
                Route::post('/update', 'MenuController@update')->name('menu.menu.update');
                Route::get('/delete', 'MenuController@delete')->name('menu.menu.delete');
            });
            Route::prefix('media')->group(function () {
                Route::get('/', 'MediaController@index')->name('media.folder.index');
                Route::get('/folder/store', 'MediaController@folderAdd')->name('media.folder.add');
                Route::post('/folder/update', 'MediaController@folderUpdate')->name('media.folder.update');
                Route::get('/folder', 'MediaController@folder')->name('media.folder');
                Route::post('/folder/move', 'MediaController@folderMove')->name('media.folder.move');
                Route::post('/folder/delete', 'MediaController@folderDelete')->name('media.folder.delete');;

                Route::post('/file/store', 'MediaController@fileAdd')->name('media.file.add');
                Route::get('/file', 'MediaController@file');
                Route::post('/file/delete', 'MediaController@fileDelete')->name('media.file.delete');
                Route::post('/file/update', 'MediaController@fileUpdate')->name('media.file.update');
                Route::post('/file/move', 'MediaController@fileMove')->name('media.file.move');
                Route::post('/file/cropp', 'MediaController@cropp');
                Route::get('/file/copy', 'MediaController@fileCopy')->name('media.file.copy');
            });
            Route::prefix('configs')->group(function () {
                Route::get('/', 'AppConfigController@index')->name('configs');
                Route::get('/{id}', 'AppConfigController@edit')->name('configs.edit');
                Route::put('/{id}', 'AppConfigController@update')->name('configs.update');
            });
        });
    });
});
