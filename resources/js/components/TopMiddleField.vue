<style>
  div.file-listing{
    display: inline-block;
    position: relative;
    width: 200px;
    margin: auto;
    padding: 10px;
  }
  div.file-listing img{
    height: 100px;
  }
  span.remove-container{
    position: absolute;
    top:0;
    right:10px;
  }
  span.remove-container a,
  span.remove-container a:hover{
    color: red;
    cursor: pointer;
  }
  progress{
    width: 100%;
    margin: auto;
    display: block;
    margin-top: 20px;
    margin-bottom: 20px;
  }
  .filename {
    width: 170px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .top-center-a {
    font-size: 1.8rem !important;
    opacity: .9 !important;
  }
  .top-center a {
    cursor: pointer;
    text-align: center;
    font-weight: 900;
    color: #FDEE30;
    opacity: .5;
  }
  .top-center a:hover,
  .top-center a:visited {
    color: #FDEE30;
  }
  #images {
    display: none;
  }
</style>

<template>
    <div class="top-center">
        <label for="images">
        <a class="top-center-a">+</a></label>
        <input type="file" name="images" id="images" multiple @change="onButtonChange($event)">
        <!-- Modal -->
        <div class="modal fade" id="previewTopModal" tabindex="-1" role="dialog" aria-labelledby="previewTopModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">Preview before upload</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-for="(image, key) in images" :key="key" class="file-listing">
                            <img class="preview" v-bind:ref="'preview'+parseInt( key )"/>
                            <div class="filename">
                                {{ image.name }}
                            </div>
                            <span class="remove-container">
                                <a class="remove" v-on:click="removeFiles( key )"><i class="fas fa-trash"></i></a>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <div class="folder-info">
                            <label for="special-tag">Add special tag:</label>
                                <input name="special-tag" id="special-tag" @input="onSpecialTagInput($event)">
                        </div>
                        <div class="folder-info">
                            <label for="year">Select year of these photos:</label>
                            <select name="year" id="year" @change="onSelectChange($event)">
                                <option v-for="year in years" :value="year" :key="year">{{ year }}</option>
                            </select>
                        </div>
                        <a class="btn btn-primary" v-on:click="submitFiles()" v-show="images.length > 0">Upload</a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <progress max="100" :value.prop="uploadPercentage"></progress>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    data(){
      return {
        images: [],
        uploadPercentage: 0,
        yearSelect: 0,
        specialTag: '',
      }
    },

    computed: {
      years: function () {
        const year = new Date().getFullYear()
        return Array.from({length: year - 1980}, (value, index) => year - index)
      }
    },

    mounted(){
      this.yearSelect = new Date().getFullYear();
    },

    methods: {
      onSelectChange(event) {
        this.yearSelect = event.target.value;
      },
      onSpecialTagInput(event) {
        this.specialTag = event.target.value;
      },
      onButtonChange(event) {
        for( let i = 0; i < event.srcElement.files.length; i++ ){
          this.images.push( event.srcElement.files[i] );
        }
        this.getImagePreviews();
      },

      /*
        Gets the image preview for the file.
      */
      getImagePreviews(){
        /*
          Iterate over all of the files and generate an image preview for each one.
        */
        for( let i = 0; i < this.images.length; i++ ){
          /*
            Ensure the file is an image file
          */
          if ( /\.(jpe?g|png|gif)$/i.test( this.images[i].name ) ) {
            /*
              Create a new FileReader object
            */
            let reader = new FileReader();

            /*
              Add an event listener for when the file has been loaded
              to update the src on the file preview.
            */
            reader.addEventListener("load", function(){
              this.$refs['preview'+parseInt( i )][0].src = reader.result;
            }.bind(this), false);


            /*
              Read the data for the file in through the reader. When it has
              been loaded, we listen to the event propagated and set the image
              src to what was loaded from the reader.
            */
            reader.readAsDataURL( this.images[i] );
          }else{
            /*
              We do the next tick so the reference is bound and we can access it.
            */
            this.$nextTick(function(){
              this.$refs['preview'+parseInt( i )][0].src = '/images/file.png';
            });
          }
        }
        $("#previewTopModal").modal('show');
      },

      /*
        Submits the files to the server
      */
      submitFiles(){
        /*
          Initialize the form data
        */
        let formData = new FormData();

        /*
          Iteate over any file sent over appending the files
          to the form data.
        */
        for( var i = 0; i < this.images.length; i++ ){
          let file = this.images[i];

          formData.append('files[' + i + ']', file);
        }

        formData.append('year', this.yearSelect);
        formData.append('specialTag', this.specialTag);
        /*
          Make the request to the POST /file-drag-drop URL
        */
        axios.post( '/file-upload',
          formData,
          {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: function( progressEvent ) {
              this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
            }.bind(this)
          }
        ).then(function(){
            $("#previewTopModal").modal('hide');
            window.location.href = 'http://photog.local/home';
            console.log('SUCCESS - uploaded!');
        })
        .catch(function(){
          console.log('FAILURE!!');
        });
      },

      /*
        Removes a select file the user has uploaded
      */
      removeFiles( key ){
        this.images.splice( key, 1 );
        this.getImagePreviews();
      }
    }
  }
</script>
