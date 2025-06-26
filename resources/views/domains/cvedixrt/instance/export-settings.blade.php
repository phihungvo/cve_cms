@php
    use Illuminate\Support\Carbon;
@endphp

@extends('layouts.in')

@section('body')
    <div class="intro-y box p-5 mt-5" x-data="{
            tabs: ['Email', 'MQTT', 'Webhook'],
            activeTab: 'Email',
            ready: false,
            init() { this.ready = true }
        }" x-init="init()" x-cloak>
        <h2 class="text-2xl font-bold py-2">{{__('cvedixrt-instance-export-settings.export-settings')}}</h2>
        <!-- Email -->
        <div class="p-2" x-data="{open: false}">
            <!-- Email control -->
            <div class="flex items-center">
                <input type="checkbox" name="email" id="email" class="form-checkbox form-check-switch" value="email" autocomplete="false"
                x-on:change="open = $event.target.checked">
                <label for="email" class="text-xl font-medium text-left cursor-pointer hover:font-bold p-2 ">{{__('cvedixrt-instance-export-settings.email')}}</label>
            </div>
            <!-- Email content -->
            <div x-show="open" x-transition
            class="w-full p-2 border border-2 rounded border-gray-200"
            >
                <div class="mb-4">
                    <label for="smtp_host" class="block font-medium mb-1">SMTP Host</label>
                    <input id="smtp_host" name="smtp_host" type="text" class="form-control form-control-lg" placeholder="smtp.example.com">
                </div>
                <div class="mb-4">
                    <label for="smtp_port" class="block font-medium mb-1">SMTP Port</label>
                    <input id="smtp_port" name="smtp_port" type="number" class="form-control form-control-lg" placeholder="587">
                </div>
                <div class="mb-4">
                    <label for="encryption" class="block font-medium mb-1">Encryption</label>
                    <select id="encryption" name="encryption" class="form-select w-full">
                        <option value="none">None</option>
                        <option value="ssl">SSL</option>
                        <option value="tls">TLS</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="username" class="block font-medium mb-1">Username</label>
                    <input id="username" name="username" type="text" class="form-control form-control-lg" placeholder="user@example.com">
                </div>
                <div class="mb-4">
                    <label for="password" class="block font-medium mb-1">Password</label>
                    <input id="password" name="password" type="password" class="form-control form-control-lg" placeholder="Password">
                </div>
                <div class="mb-4">
                    <label for="from_email" class="block font-medium mb-1">From Email</label>
                    <input id="from_email" name="from_email" type="email" class="form-control form-control-lg" placeholder="from@example.com">
                </div>
                <div class="mb-4">
                    <label for="from_name" class="block font-medium mb-1">From Name</label>
                    <input id="from_name" name="from_name" type="text" class="form-control form-control-lg" placeholder="Sender Name">
                </div>
            </div>
        </div>

        <!-- MQTT -->
        <div class="p-2" x-data="{open: false}">
            <!-- Email control -->
            <div class="flex items-center">
                <input type="checkbox" name="mqtt" id="mqtt" class="form-checkbox form-check-switch" value="mqtt"
                x-on:change="open = $event.target.checked">
                <label for="mqtt" class="text-xl font-medium text-left cursor-pointer hover:font-bold p-2">{{__('cvedixrt-instance-export-settings.mqtt')}}</label>
            </div>
            <!-- MQTT content -->
            <div x-show="open" x-transition
                 class="w-full p-2 border border-2 rounded border-gray-200"
            >
                <div class="mb-4">
                    <label for="mqtt_host" class="block font-medium mb-1">MQTT Host</label>
                    <input id="mqtt_host" name="mqtt_host" type="text" class="form-control form-control-lg" placeholder="mqtt.example.com">
                </div>
                <div class="mb-4">
                    <label for="mqtt_port" class="block font-medium mb-1">MQTT Port</label>
                    <input id="mqtt_port" name="mqtt_port" type="number" class="form-control form-control-lg" placeholder="1883">
                </div>
                <div class="mb-4">
                    <label for="mqtt_username" class="block font-medium mb-1">Username</label>
                    <input id="mqtt_username" name="mqtt_username" type="text" class="form-control form-control-lg" placeholder="MQTT Username">
                </div>
                <div class="mb-4">
                    <label for="mqtt_password" class="block font-medium mb-1">Password</label>
                    <input id="mqtt_password" name="mqtt_password" type="password" class="form-control form-control-lg" placeholder="MQTT Password">
                </div>
                <div class="mb-4">
                    <label for="mqtt_topic" class="block font-medium mb-1">Topic</label>
                    <input id="mqtt_topic" name="mqtt_topic" type="text" class="form-control form-control-lg" placeholder="topic/example">
                </div>
            </div>
        </div>

        <!-- Webhook -->
        <div  class="p-2" x-data="{open: false}">
            <!-- Webhook control -->
            <div class="flex items-center">
                <input type="checkbox" name="webhook" id="webhook" class="form-checkbox form-check-switch" value="webhook"
                x-on:change="open = $event.target.checked">
                <label for="webhook" class="text-xl font-medium text-left cursor-pointer hover:font-bold p-2">{{__('cvedixrt-instance-export-settings.webhook')}}</label>
            </div>
            <!-- Webhook content -->
            <div x-show="open" x-transition
                 class="w-full p-2 border border-2 rounded border-gray-200"
            >
                <div class="mb-4">
                    <label for="webhook_url" class="block font-medium mb-1">Webhook URL</label>
                    <input id="webhook_url" name="webhook_url" type="url" class="form-control form-control-lg" placeholder="https://example.com/webhook">
                </div>
                <div class="mb-4">
                    <label for="webhook_method" class="block font-medium mb-1">HTTP Method</label>
                    <select id="webhook_method" name="webhook_method" class="form-select w-full">
                        <option value="POST">POST</option>
                        <option value="GET">GET</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="webhook_headers" class="block font-medium mb-1">Headers (JSON)</label>
                    <input id="webhook_headers" name="webhook_headers" type="text" class="form-control form-control-lg" placeholder='{"Authorization": "Bearer ..."}'>
                </div>
                <div class="mb-4">
                    <label for="webhook_body" class="block font-medium mb-1">Body (JSON)</label>
                    <textarea id="webhook_body" name="webhook_body" class="form-control form-control-lg resize-none" rows="5" placeholder='{"key": "value"}' ></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end items-center mt-4" x-show="ready">
            <!-- Btn Cancel -->
            <a href="{{route('cvedixrt_instance.index')}}" class="inline-block btn btn-secondary">{{__('cvedixrt-instance-export-settings.btn-cancel')}}</a>
        </div>
    </div>
@endsection
