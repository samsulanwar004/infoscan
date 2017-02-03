<form id="modalForm" action="{{ admin_route_url('snaps.update', ['id' => $snapFile->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <input type="hidden" name="mode" value="{{ $snapFile->mode_type }}">
    <div class="modal-header">
        <a class="close btn-close btn-modal-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Snap </span> File {{ strtoupper($snapFile->mode_type) }}</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-4">
                <div id="imgtag">
                    <img src="{{ config('filesystems.s3url') . $snapFile->file_path }}" id="tag-image" alt="{{ $snapFile->id }}" class="margin img-responsive">
                    <div id="tagbox">
                    </div>                    
                </div>                
            </div>
            <div class="col-md-8 table-custom" style="overflow-y:scroll;max-height: 300px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50"></th>
                            <th width="300">Product Item</th>
                            <th width="300">Brands</th>
                            <th width="300">Variants</th>
                            <th width="50">Qty</th>
                            <th width="200">Total Price</th>
                        </tr>
                    </thead>
                    <tbody id="inputs">
                        @foreach($snapFile->tag as $tag)
                            <tr id="input">
                                <td>
                                    <a class="{{ $tag->id }}-tag btn btn-box-tool" id="remove">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                </td>
                                <td width="300"><input type="text" name="tag[name][]" id="{{ $tag->id }}|{{ $tag->img_x }}|{{ $tag->img_y }}" class="form-control input-sm tag-name {{ $tag->id }}old" value="{{ $tag->name }}" placeholder="Product Name" required="required"></td>
                                <td width="300"><input type="text" name="tag[brands][]" class="form-control input-sm" value="{{ $tag->brands }}" placeholder="Brands"></td>
                                <td width="300"><input type="text" list="variants" name="tag[variants][]" class="form-control input-sm" value="{{ $tag->variants }}" placeholder="Variants"></td>
                                <td width="100"><input type="number" name="tag[qty][]" class="form-control input-sm" value="{{ $tag->quantity }}" placeholder="QTY" required="required"></td>
                                <td width="200"><input type="number" name="tag[total][]" class="form-control input-sm" value="{{ clean_numeric($tag->total_price,'%',false,'.') }}" placeholder="Total Price" required="required"></td>
                                <input type="hidden" name="tag[id][]" value="{{ $tag->id }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <datalist id="variants">
                  <option value="PC Packaging: Roll On| PC Weight: ">
                  <option value="PC Packaging: Spray| PC Weight: ">
                  <option value="PC Packaging: Pack| PC Weight: ">
                  <option value="PC Packaging: Bottle| PC Weight: ">
                  <option value="PC Packaging: Sachet| PC Weight: ">
                </datalist>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="button-container">
            <a class="btn btn-link btn-close" data-dismiss="modal">Close</a>
            <button class="btn btn-primary submit-to-server">
                <i class="fa fa-save fa-btn"></i> <span class="ladda-label">Save Item</span>
            </button>
            <div class="la-ball-fall">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>
</form>

<style type="text/css">
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button
    {
      -webkit-appearance: none;
      margin: 0;
    }

    .modal-dialog {
        width: 90%;
    }

    #imgtag
    {
        position: relative;
        min-width: 300px;
        min-height: 300px;
        float: none;
        border: 3px solid #FFF;
        cursor: crosshair;
        text-align: center;
    }
    .tagview
    {
        border: 1px solid #F10303;
        width: 100px;
        height: 80px;
        position: absolute;
    /*display:none;*/
        opacity: 0;
        color: #FFFFFF;
        text-align: center;
    }
    .square
    {
        display: block;
        height: 79px;
    }
    .person
    {
        height: 80px;
        background-color: rgba(0, 0, 0, 0.6);
        border-top: 1px solid #F10303;
    }
    #tagit
    {
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        width: 200px;
        border: 1px solid #D7C7C7;
        background-color: rgba(0, 0, 0, 0.6);
        padding: 7px;
    }
    #tagit div.text
    {
        margin-bottom: 5px;
    }
    #tagit input
    {
        margin-bottom: 5px;
    }
    #tagit input[type=button]
    {
        margin-right: 5px;
    }

    a.taggd__button
    {
        cursor: pointer;
    }

</style>

<script type="text/javascript">
    $(document).ready(function() {
        $('#modalForm').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
                REBEL.removeAllMessageAlert();
                if (responseData.status == "ok") {
                    REBEL.smallNotifTemplate(responseData.message, '.modal-content', 'success');
                    $.get( '/snaps/{{ $snapFile->snap_id }}/snap-detail' , function(view){ 
                        $(".snaps-detail").html(view);
                        var totalValue = $('#snap-table').attr('totalValue');
                        $('#total_value').val(totalValue);
                    });
                }
            }, true);
        });

    $('.tag-name').on('click', function(e) {
        var image = document.getElementById('tag-image');
        var idArray = e.toElement.id.split('|');
        var id = idArray[0];
        var img_x = idArray[1];
        var img_y = idArray[2];
        $("#"+id+"popup").css({
            'display' : 'none',
        });
        var options = {};
        var data = [
            Taggd.Tag.createFromObject({
                position: { x: img_x, y: img_y },
                text: this.value,
                popupAttributes: {
                    id: id+"popup",
                },
            }),
        ];

        var taggd = new Taggd(image, options, data);

        $("#"+id+"popup").css({
            'display' : '',
        });

        $('.'+id+'old').on('keyup', function() {
            $("#"+id+"popup").html($("."+id+'old').val());
        });
    });

    $('form').on('focus', 'input[type=number]', function(e) {
      $(this).on('mousewheel.disableScroll', function(e) {
        e.preventDefault()
      })
    });
    $('form').on('blur', 'input[type=number]', function(e) {
      $(this).off('mousewheel.disableScroll')
    });

        var counter = 0;
        var mouseX = 0;
        var mouseY = 0;

        $("#imgtag img").click(function(e) { // make sure the image is click
            var offset = $(this).offset(); // get the div to append the tagging list
            mouseX = (e.pageX - offset.left); // x and y axis
            mouseY = (e.pageY - offset.top);

            $('#tagit').remove(); // remove any tagit div first           
            $('div#imgtag').append('<div id="tagit"><input type="text" name="name" class="form-control input-sm" placeholder="Product Name" id="name"><input type="number" name="qty" class="form-control input-sm" placeholder="QTY" id="qty"><input type="number" class="form-control input-sm" placeholder="Total Price" id="total" name="total"><input type="hidden" name="x" id="x" value="'+mouseX+'"><input type="hidden" id="y" name="y" value="'+mouseY+'"><input type="button" name="btnsave" value="Save" id="btnsave"/><input type="button" name="btncancel" value="Cancel" id="btncancel" /></div>');
            var imgtag = document.getElementById('imgtag');
            var tagit = document.getElementById('tagit');
            var tengah = imgtag.clientHeight/2;

            if (mouseY > tengah) {                
                $('#tagit').css({ top:mouseY-tagit.clientHeight, left:mouseX });          
            } else {
                $('#tagit').css({ top:mouseY, left:mouseX });
            }
            $('#name').focus();

        });


        $(document).on('click', '#tagit #btnsave', function(e) {
            e.stopPropagation();

            var time = Math.round(Date.now() / 100);

            if ($('.tag-input[time=' + time + ']').length > 0) {
                return false;
            }
            
            e.preventDefault();
            var countOfTextbox = $('.tag-name').length;

            if(countOfTextbox >= 20) {
                REBEL.smallNotifTemplate('Form is full', '.modal-content', 'error');
                REBEL.scrollToTop('#modalContent');
                setTimeout(function(){
                    REBEL.removeAllMessageAlert();
                }, 3000);
                return false;
            }

            var name = $('#name').val();
            var qty = $('#qty').val();
            var total = $('#total').val();
            mouseX = $('#x').val();
            mouseY = $('#y').val();

            if (name == false || qty == false || total == false)
            {
                REBEL.smallNotifTemplate('Not null', '.modal-content', 'error');
                REBEL.scrollToTop('#modalContent');
                setTimeout(function(){
                    REBEL.removeAllMessageAlert();
                }, 3000);
                return false;
            }

            var image = document.getElementById('tag-image');
            mouseX = mouseX / image.clientWidth;
            mouseY = mouseY / image.clientHeight;

            var className = countOfTextbox+'-new-tag';
            viewtagsave(name, mouseX, mouseY, className);
            $('tbody#inputs').append('<tr class="tag-input" time=' + time + ' id="input'+countOfTextbox+'"><td><a class="btn btn-box-tool" onclick="deleteTag('+countOfTextbox+')"><i class="fa fa-remove"></i></a></td><td width="300"><input type="text" name="newtag[name][]" class="form-control input-sm tag-name '+countOfTextbox+'new" id="'+countOfTextbox+'|'+mouseX+'|'+mouseY+'" onclick="editTag(this)" onkeyup="editNewTag(this)" value="'+name+'"></td><td width="300"><input type="text" name="newtag[brands][]" class="form-control input-sm" placeholder="Brands"></td><td width="300"><input type="text" list="variants" name="newtag[variants][]" class="form-control input-sm" placeholder="Variants"></td><td width="100"><input type="number" name="newtag[qty][]" class="form-control input-sm" value="'+qty+'"></td><td width="200"><input type="number" name="newtag[total][]" class="form-control input-sm" value="'+total+'"><input type="hidden" name="newtag[x][]" value="'+mouseX+'"><input type="hidden" name="newtag[y][]" value="'+mouseY+'"><input type="hidden" name="newtag[fileId][]" value="{{ $snapFile->id }}"></td></tr>');

            $('#tagit').fadeOut();
        });

        // Cancel the tag box.
        $(document).on('click', '#tagit #btncancel', function() {
          $('#tagit').fadeOut();
        });

        // mouseover the tagboxes that is already there but opacity is 0.
        $('#tagbox').on('mouseover', '.tagview', function() {
            var pos = $( this ).position();
            $(this).css({ opacity: 1.0 }); // div appears when opacity is set to 1.
        }).on('mouseout', '.tagview', function() {
            $(this).css({ opacity: 0.0 }); // hide the div by setting opacity to 0.
        });

        // load the tags for the image when page loads.
        var img = $('#imgtag').find('img');
        var id = $(img).attr('alt');
        viewtag(id);
        function viewtag(id)
        {
          // get the tag list with action remove and tag boxes and place it on the image.
            var image = document.getElementById('tag-image');
            var data =[];
            var options = {};
            var taggd;
            $.getJSON( id+"/tagging" , function( datas ) {
                $.each( datas, function( key, value ) {

                    data.push(
                        Taggd.Tag.createFromObject({
                            position: { x: value.img_x, y: value.img_y },
                            text: value.name,
                            buttonAttributes: {
                                id: value.id+"-tag",
                            },
                        })
                    );
                });
                taggd = new Taggd(image, options, data);
            }, "json");
        }

        function viewtagsave(name, mouseX, mouseY, className)
        {
            var image = document.getElementById('tag-image');
            var data =[];
            var options = {};
            var taggd;

            var data = [
                Taggd.Tag.createFromObject({
                    position: { x: mouseX, y: mouseY },
                    text: name,
                    buttonAttributes: {
                        id: className,
                    },
                }),
            ];

            taggd = new Taggd(image, options, data);
        }

        $('a#remove').on('click', function(e) {
            e.preventDefault();
            var classArray = this.className.split(' ');
            var id = classArray[0];
            if(confirm('Are you sure want to delete this item ?')) {
                $(e.target).closest('#input').remove();
                $("#"+id).remove();
            }
        });
    });

    function deleteTag(e)
    {
        if(confirm('Are you sure want to delete this item ?')) {
            $('#input'+e).remove();
            $("#"+e+"-new-tag").remove();
        }
    }

    function editTag(e)
    {
        var image = document.getElementById('tag-image');
        var idArray = e.id.split('|');
        var id = idArray[0];
        var img_x = idArray[1];
        var img_y = idArray[2];
        $("#"+id+"newpopup").css({
            'display' : 'none',
        });
        var options = {};
        var data = [
            Taggd.Tag.createFromObject({
                position: { x: img_x, y: img_y },
                text: e.value,
                popupAttributes: {
                    id: id+"newpopup",
                },
            }),
        ];

        var taggd = new Taggd(image, options, data);

        $("#"+id+"newpopup").css({
            'display' : '',
        });
    }

    function editNewTag(e)
    {
        var idArray = e.id.split('|');
        var id = idArray[0];
        $("#"+id+"newpopup").html($("."+id+'new').val());
    }

</script>
