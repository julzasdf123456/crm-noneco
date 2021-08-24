@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h5>Set Roles for {{ $user->name }}</h5>
                </div>
            </div>
        </div>
    </section>
    
    <div>
        <form class="form" method="POST" action="{{ url('users/create-user-permissions') }}">
            <!-- SUBMIT -->
            <button type="submit" class="btn btn-sm btn-info" style="margin-bottom: 15px;"><i class="fas fa-fw fa-check"></i> Set</button> 

            <div class="row">
                
                @csrf
                @foreach($roles as $role)                     
                    <div class="col-md-4 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <span>{{ $role->name }}</span>
                            </div>
                            <div class="card-body">
                                @php 
                                    $permissions = $role->permissions;
                                @endphp
                                @foreach ($permissions as $permission)
                                    <div class="form-check">  
                                        <input type="checkbox" class="form-check-input" value="{{ $permission->id }}" name="permissions[]"  @if($user->getAllPermissions()) @if(in_array($permission->id, $user->getAllPermissions()->pluck('id')->toArray())) checked @endif @endif>
                                        <label for="permissions" class="form-check-label">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                                
                            </div>
                        </div>
                        
                    </div>
                @endforeach
                <!-- ADD USER ID -->
                <input type="hidden" name="userId" value="{{ $user->id }}">               
            
            </div>
        </form>
    </div>
@endsection