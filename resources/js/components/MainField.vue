<style>
  .form-upload{
    display: block;
    height: 200px;
    width: 80%;
    border: 2px dashed #ccc;
    background: #FFFFFF;
    margin: auto;
    margin-top: 40px;
    text-align: center;
    line-height: 200px;
    border-radius: 4px;
    font-weight: 600;
  }
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
  #files {
    display: none;
  }
  .clickme {
    cursor: pointer;
    color: #FDEE30 !important;
  }
  @media (max-width: 768px) {
      .container {
          background-color: #FFFFFF;
      }
  }
</style>

<template>
  <div class="container">
    <div id="file-drag-drop">
        <form ref="fileform" class="form-upload">
            <label for="files">
            Drop images here or <a class="clickme">click me!</a></label>
            <input type="file" name="files" id="files" multiple @change="onButtonChange($event)">
        </form>
        <!-- Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview before upload</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-for="(file, key) in files" :key="key" class="file-listing">
                        <img class="preview" v-bind:ref="'preview'+parseInt( key )"/>
                        <div class="filename">
                            {{ file.name }}
                        </div>
                        <span class="remove-container">
                            <a class="remove" v-on:click="removeFile( key )"><i class="fas fa-trash"></i></a>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                  <div class="folder-info">
                            <label for="special-tags">Add special tag:</label>
                                <input name="special-tags" id="special-tags" @input="onSpecialTagsInput($event)">
                        </div>
                    <div class="folder-info">
                        <label for="year">Select year of these photos:</label>
                        <select name="year" id="year" @change="onSelectChange($event)">
                            <option v-for="year in years" :value="year" :key="year">{{ year }}</option>
                        </select>
                    </div>
                    <a class="btn btn-primary" v-on:click="submitFiles()" v-show="files.length > 0">Upload</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <progress max="100" :value.prop="uploadPercentage"></progress>
                </div>
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
        dragAndDropCapable: false,
        files: [],
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
      /*
        Determine if drag and drop functionality is capable in the browser
      */
      this.dragAndDropCapable = this.determineDragAndDropCapable();

      /*
        If drag and drop capable, then we continue to bind events to our elements.
      */
      if( this.dragAndDropCapable ){
        /*
          Listen to all of the drag events and bind an event listener to each
          for the fileform.
        */
        ['drag', 'dragstart', 'dragend', 'dragover', 'dragenter', 'dragleave', 'drop'].forEach( function( evt ) {
        /*
          For each event add an event listener that prevents the default action
          (opening the file in the browser) and stop the propagation of the event (so
          no other elements open the file in the browser)
        */
        this.$refs.fileform.addEventListener(evt, function(e){
          e.preventDefault();
          e.stopPropagation();
        }.bind(this), false);
      }.bind(this));

      /*
        Add an event listener for drop to the form
      */
      this.$refs.fileform.addEventListener('drop', function(e){
        /*
          Capture the files from the drop event and add them to our local files
          array.
        */
        for( let i = 0; i < e.dataTransfer.files.length; i++ ){
          this.files.push( e.dataTransfer.files[i] );
          this.getImagePreviews();
        }
      }.bind(this));
    }
  },

    methods: {
      onSelectChange(event) {
        this.yearSelect = event.target.value;
      },
      onButtonChange(event) {
        for( let i = 0; i < event.srcElement.files.length; i++ ){
          this.files.push( event.srcElement.files[i] );
          this.getImagePreviews();
        }
      },
      onSpecialTagsInput(event) {
        this.specialTag = event.target.value;
      },
      /*
        Determines if the drag and drop functionality is in the
        window
      */
      determineDragAndDropCapable(){
        /*
          Create a test element to see if certain events
          are present that let us do drag and drop.
        */
        var div = document.createElement('div');

        /*
          Check to see if the `draggable` event is in the element
          or the `ondragstart` and `ondrop` events are in the element. If
          they are, then we have what we need for dragging and dropping files.

          We also check to see if the window has `FormData` and `FileReader` objects
          present so we can do our AJAX uploading
        */
        return ( ( 'draggable' in div )
                || ( 'ondragstart' in div && 'ondrop' in div ) )
                && 'FormData' in window
                && 'FileReader' in window;
      },

      /*
        Gets the image preview for the file.
      */
      getImagePreviews(){
        /*
          Iterate over all of the files and generate an image preview for each one.
        */
        for( let i = 0; i < this.files.length; i++ ){
          /*
            Ensure the file is an image file
          */
          if ( /\.(jpe?g|png|gif)$/i.test( this.files[i].name ) ) {
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
            reader.readAsDataURL( this.files[i] );
          }else{
            /*
              We do the next tick so the reference is bound and we can access it.
            */
            this.$nextTick(function(){
              this.$refs['preview'+parseInt( i )][0].src = '/images/file.png';
            });
          }
        }
        // console.log(this.files);
        $("#previewModal").modal('show');
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
        for( var i = 0; i < this.files.length; i++ ){
          let file = this.files[i];

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
            $("#previewModal").modal('hide');
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
      removeFile( key ){
        this.files.splice( key, 1 );
        this.getImagePreviews();
      }
    }
  }
</script>
