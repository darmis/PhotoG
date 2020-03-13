<style>
  #images-container {
    width: 100%;
    display: flex; /* or inline-flex */
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-around;
  }
  .image {
    width: 250px;
    height: 150px;
    margin: 1rem;
  }
  .image-item {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .image:hover {
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.3), 0 3px 10px 0 rgba(0, 0, 0, 0.29);
    cursor: pointer;
  }
  .spinner-grow {
    width: 5rem;
    height: 5rem;
  }
</style>
<template>
<div>
  <div id="images-container">
    <div v-for="(src, index) in imgs" :key="index" class="image" @click="() => showImg(index)">
        <img class="image-item" :src="src">
    </div>
  </div>
  <div class="spinners" v-show="loading">
    <div class="spinner-grow text-secondary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-secondary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    <div class="spinner-grow text-secondary" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <div class="slider" v-show="slider">
      <div class="slider-view">
          <img id="img" :src="src" alt=""/>
      </div>
      <button class="prev" id="prev" type="button" @click="() => previous()" v-show="currentImg !== 0">◀</button>
      <button class="next" id="next" type="button" @click="() => next()"  v-show="currentImg !== imgs.length-1">▶</button>
      <button class="close-gallery" type="button" @click="() => closeSlider()">Close</button>
  </div>
</div>
</template>

<script>

export default {
  props:['tag'],
  data() {
    return {
      loading: false,
      slider: false,
      imgs: [],
      page: 1,
      currentPage: 1,
      lastPage: 999,
      src: '',
      currentImg: 0,
    }
  },
  mounted(){
    // Initially load some items.
    this.loadPhotos(this.imgs);
      
    document.onscroll = () => {
      if(document.documentElement.scrollTop + window.innerHeight === document.documentElement.offsetHeight){
        if(this.currentPage < this.page && this.currentPage <= this.lastPage){
          this.loadPhotos(this.imgs);
        }
      }
    }
  },
  methods: {
    loadPhotos(imgs){
      let self = this;
      self.loading = true;
      self.currentPage = self.page;
      if(self.page <= self.lastPage){
        axios.get('/loadPhotos/' + this.tag + '?page=' + this.page)
          .then(function (response) {
            response.data.data.forEach(function(item) {
              imgs.push( '/storage' + item.path );
            });
            self.lastPage = response.data.last_page;
            self.page++;            
          })
          .catch(function (error) {
            console.log(error);
        });
      }
      self.loading = false;
    },
    showImg (index) {
      this.currentImg = index;
      let self = this;
      self.slider = true;
      self.src = self.imgs[index];
    },
    closeSlider(){
      this.slider = false;
    },
    next(){
      this.currentImg++;
      this.showImg(this.currentImg);
    },
    previous() {
      this.currentImg--;
      this.showImg(this.currentImg);
    }
  }
}
</script>