@extends('layouts.app')


@section('content')
    <form action="{{ route('purchases.store') }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('POST') }}
        <br>
        <center>
            <h1> Buy A Package For a Member</h1>
        </center>
        <br>
        <div class="form-group row">
            <label for="package" class="col-sm-4 col-form-label">Training Package</label>
            <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="package" name="package">
                    @foreach ($packages as $package)
                        <option value="{{ $package->id }}">{{ $package->name }} -
                            {{ number_format($package->price, 2, ',', '.') }}$</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="user" class="col-sm-4 col-form-label">Member</label>
            <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="user" name="user">
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row">
            <Button class="btn btn-success" type="submit">Submit</Button>
        </div>
        </div>

    </form>
@endsection
