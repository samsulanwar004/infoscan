@extends('emails.templates.app')

@section('title')
Member E-mail Verification
@endsection

@section('content')
	<table>
		<tr>
			<td>
				<h3>Hi, {{ $member->name }}</h3>
				<p class="lead">Terima kasih telah menukarkan poin anda. </p>
				<p>
					Berikut Loyalty yang anda terima :
				</p>
				<!-- Callout Panel -->
				<p class="callout">
					Transaction Time : {{ $order->trx_time }}<br>
					Order Number : {{ $order->order_number }}<br>
					Brand : {{ $order->brand }}<br>
					EGIFT CODE : {{ $order->egift_code }}<br>
					Program Name : {{ $order->program_name }}<br>
					Value : {{ $order->value }}<br>
					Expired Date : {{ $order->expired_date }}<br>
				</p><!-- /Callout Panel -->

				<p>Foto terus belanjaanmu untuk mengumpulkan poin!</p>
				<p>Salam, <br>Tim Gojago</p>
			</td>
		</tr>
	</table>
@endsection