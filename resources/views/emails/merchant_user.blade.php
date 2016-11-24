@extends('emails.templates.app')

@section('title')
Merchant User Account
@endsection

@section('content')
	<table>
		<tr>
			<td>
				<h3>Hi, {{ $user['user']->name }}</h3>
				<p class="lead">Here is your access rights.</p>
				<p>Name : {{ $user['user']->name }}</p>
				<p>Email : {{ $user['user']->email }}</p>
				<p>Password : {{ $user['password'] }}</p>
				<!-- Callout Panel -->
				<p class="callout">
					Login here. <a href="#">Click it! &raquo;</a>
				</p><!-- /Callout Panel -->						
			</td>
		</tr>
	</table>
@endsection