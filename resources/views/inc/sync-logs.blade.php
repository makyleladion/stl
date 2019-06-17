@extends('layouts.main') @section('content')

<div id="project-dashboard" class="page-layout blank p-6">

    <div class="page-content-wrapper">
    
    	<div class="widget widget1 card">
    	
    			@foreach ($logs as $log)
    			
    			echo $log['sync_time']." - " .$log['result'];
    			
    			@endforeach
    	
    	</div>
    
    </div>
    
</div>