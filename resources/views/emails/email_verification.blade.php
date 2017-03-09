@extends('emails.templates.app')

@section('title')
Member E-mail Verification
@endsection

@section('content')
	<table>
		<tr>
			<td>
				<h3>Hi, {{ $member->name }}</h3>
				<p class="lead">Terima kasih telah mengunduh aplikasi Gojago! </p>
				<p>
					Silahkan aktifkan aku Anda melalui link di bawah ini
				</p>
				<!-- Callout Panel -->
				<p class="callout">
					<a href="{{ route('member_verification', ['token' => $member->verification_token]) }}">Verifikasi disini! &raquo;</a>
				</p><!-- /Callout Panel -->

				<p>Foto terus belanjaanmu untuk mengumpulkan poin!</p>
				<p>Salam, <br>Tim Gojago</p>
			</td>
		</tr>
	</table>
@endsection