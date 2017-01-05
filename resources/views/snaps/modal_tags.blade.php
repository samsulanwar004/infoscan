<form id="modalForm" action="{{ admin_route_url('snaps.update', ['id' => $snapFile->id]) }}"  method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <input type="hidden" name="mode" value="{{ $snapFile->mode_type }}">
    <div class="modal-header">
        <a class="close btn-close" data-dismiss="modal">&times;</a>
        <h4><i class="fa fa-file-o fa-btn"></i> <span class="action-title">Snap </span> File</h4>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div id="imgtag">
                    <img src="{{ $snapFile->file_path }}" id="tag-image" alt="{{ $snapFile->id }}" class="margin img-responsive">
                    <div id="tagbox">
                    </div>
                </div>  
                  
            </div>
            <div class="col-md-6" style="overflow-y:scroll;max-height: 300px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50"></th>
                            <th width="300">Product Item</th>
                            <th width="50">Qty</th>
                            <th width="200" class="text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody id="inputs">
                        @foreach($snapFile->tag as $tag)
                            <tr id="input">
                                <td>
                                    <a class="btn btn-box-tool" id="remove">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                </td>
                                <td width="300"><input type="text" name="tag[name][]" class="form-control input-sm tag-name" value="{{ $tag->name }}" placeholder="Product Name" required="required"></td>
                                <td width="100"><input type="number" name="tag[qty][]" class="form-control input-sm" value="{{ $tag->quantity }}" placeholder="QTY" required="required"></td>
                                <td width="200" class="text-right"><input type="number" name="tag[total][]" class="form-control input-sm" value="{{ $tag->total_price }}" placeholder="Total Price" required="required"></td>
                                <input type="hidden" name="tag[id][]" value="{{ $tag->id }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

    a.taggd__button {
        cursor: pointer;
    }

</style>

<script type="text/javascript">

    $("modalForm").ready(function() {

        $(document).on("click", ".btn-close", function(){
            location.reload(true);
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
              $(imgtag).append('<div id="tagit"><input type="text" name="name" class="form-control input-sm" placeholder="Product Name" id="name"><input type="number" name="qty" class="form-control input-sm" placeholder="QTY" id="qty"><input type="number" class="form-control input-sm" placeholder="Total Price" id="total" name="total"><input type="button" name="btnsave" value="Save" id="btnsave"/><input type="button" name="btncancel" value="Cancel" id="btncancel" /></div>');
              $('#tagit').css({ top:mouseY, left:mouseX });
              
              $('#name').focus();
              $('#qty').focus();
              $('#total').focus();

            });            

            $(document).on('click', '#btnsave', function(e) {
                e.preventDefault();
                var countOfTextbox = $('.tag-name').length;

                if(countOfTextbox >= 20) {
                    alert('Form is full');
                    return false;
                }

                var name = $('input[name="name"]').val();
                var qty = $('input[name="qty"]').val();
                var total = $('input[name="total"]').val();

                if (name == false || qty == false || total == false)
                {
                    alert('Not null');
                    return false;
                }

                var image = document.getElementById('tag-image');
                mouseX = mouseX / image.clientWidth;
                mouseY = mouseY / image.clientHeight;  

                viewtagsave(name, mouseX, mouseY);                              

                $('tbody#inputs').append('<tr id="input'+countOfTextbox+'"><td><a class="btn btn-box-tool" onclick="deleteTag('+countOfTextbox+')"><i class="fa fa-remove"></i></a></td><td width="300"><input type="text" name="newtag[name][]" class="form-control input-sm tag-name" value="'+name+'"></td><td width="100"><input type="number" name="newtag[qty][]" class="form-control input-sm" value="'+qty+'"></td><td width="200" class="text-right"><input type="number" name="newtag[total][]" class="form-control input-sm" value="'+total+'"><input type="hidden" name="newtag[x][]" value="'+mouseX+'"><input type="hidden" name="newtag[y][]" value="'+mouseY+'"><input type="hidden" name="newtag[fileId][]" value="{{ $snapFile->id }}"></td></tr>');
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
                            })
                        );
                    });
                    taggd = new Taggd(image, options, data);
                }, "json"); 
            }

            function viewtagsave(name, mouseX, mouseY)
            {
                var image = document.getElementById('tag-image');
                var data =[];
                var options = {};
                var taggd;

                var data = [
                  Taggd.Tag.createFromObject({
                    position: { x: mouseX, y: mouseY },
                    text: name,
                  }),
                ];

                taggd = new Taggd(image, options, data);
            }

            $('a#remove').on('click', function(e) {
                e.preventDefault();
                if(confirm('Are you sure want to delete this item ?')) {
                    $(e.target).closest('#input').remove();
                }
            });
    });

    function deleteTag(e)
    {
        if(confirm('Are you sure want to delete this item ?')) {
            $('#input'+e).remove();
        }
    }                     

</script>
