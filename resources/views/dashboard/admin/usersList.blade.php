@extends('dashboard.base')

@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('Users') }}</div>
                    <div class="card-body">
                        @if(Session::has('message'))
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <a href="{{ route('users.create') }}" class="btn btn-primary m-2">Tambah User</a>
                        </div>
                            <div class="table-responsive">
                                <table class="table responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>E-mail</th>
                                        <th>Roles</th>
                                        <th>Email verified at</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->menuroles }}</td>
                                            <td>{{ $user->email_verified_at }}</td>
                                            <td>
                                                <a href="{{ url('/users/' . $user->id) }}" class="btn btn-block btn-primary">View</a>
                                            </td>
                                            <td>
                                                <a href="{{ url('/users/' . $user->id . '/edit') }}" class="btn btn-block btn-primary">Edit</a>
                                            </td>
                                            <td>
                                                @if( $you->id !== $user->id )
                                                    <form action="{{ route('users.destroy', $user->id ) }}" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button class="btn btn-block btn-danger">Delete User</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection

