@extends('layouts.default')

@section('pagetitle'){{__('t.views.page_titles.create_new_course')}}@endsection

@section('content')

    <b-card>
        <template #header>{{__('t.views.admin.new_course.title')}}</template>

        <form-basic action="admin.newcourse.store">

            <input-text name="name" label="{{__('t.models.course.name')}}" required autofocus></input-text>

            <input-text name="course_number" label="{{__('t.models.course.course_number')}}"></input-text>

            <row-text>
                <b-button variant="link" class="px-0" v-b-toggle.collapse-course-settings>
                    {{ __('t.views.admin.new_course.more_settings') }} <i class="fas fa-caret-down"></i>
                </b-button>
            </row-text>

            <b-collapse id="collapse-course-settings" :visible="false">

                <input-checkbox name="uses_impressions" label="{{__('t.models.course.uses_impressions')}}" value="1" switch size="lg"></input-checkbox>

                <input-hidden name="observation_count_red_threshold" value="5"></input-hidden>

                <input-hidden name="observation_count_green_threshold" value="10"></input-hidden>

            </b-collapse>

            <button-submit label="{{__('t.views.admin.new_course.create')}}"></button-submit>

        </form-basic>

    </b-card>

@endsection
