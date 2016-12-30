<section class="content">
    <div class="box">
        <div class="box-body" id="form-body">
            <table class="table table-striped" border="1">
                <thead>
                    <tr align="center">
                        @for ($i = 0; $i < $attributesCounts; $i++)
                            <th class="{{ $attributesKeys[$i]->name }}" name="{{ $attributesKeys[$i]->name }}">{{ $attributesKeys[$i]->value }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse($attributesValues as $item)
                        <tr align="center">
                            @for ($i = 0; $i < $attributesCounts; $i++)
                                @php 
                                    $name = $attributesKeys[$i]->name;
                                @endphp
                                <td>{{ $item->$name }}</td>
                            @endfor
                        </tr>
                    @empty
                        <tr align="center">
                            <td colspan="10"> There Is No Record For Report Status Data !!!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>