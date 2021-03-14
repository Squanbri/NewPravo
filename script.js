const main = new Vue({
  el: '#app',
  data: {
    mainFolders: [],
    items: [],

    path: ["Главная"],

    inputModal: null,
    model: null,

    basicName: null,

    selItem: null,

    inputSearch: '',
  },
  beforeCreate() {
    axios.post('php/showFolder.php', {path: '../folders'})
    .then(response => {
      response.data.items.forEach((item, i) => {
        this.items.push(Object.fromEntries(item))
        this.mainFolders.push(Object.fromEntries(item))

        this.sortItems(this.items)//Сортировка массива(Сперва папки, после файлы)
        this.mainFolders = this.mainFolders.filter(item => item.type === "folder")//Слева должны быть только папки(Дерево каталогов)
      })
    })
  },
  methods: {
    showFolder(item){
      if(item.type === "file") return
      axios.post('php/showFolder.php', { path: item.path })
      .then(response => {
        this.items = [] //Обнуляем массив
        response.data.items.forEach((item, i) =>  this.items.push(Object.fromEntries(item)) ) //Заполняем массив
        this.sortItems(this.items)//Сортировка массива(Сперва папки, после файлы)
      })

      this.path = ["Главная"]
      const temp = item.path.split('/')
      temp.splice(0,2)
      temp.map(item => this.path.push(item))
      console.log(this.items)
    },
    comeBack(index){
      let path = '../folders'
      for(let i=1; i<=index; i++)
        path += `/${this.path[i]}`;
      const item = { path: path }

      this.showFolder(item)
    },
    sortItems(items){
      const newItems = []
      items.forEach((item, i) => item.type === "folder" ? newItems.unshift(item) : newItems.push(item) );
      this.items = newItems
    },
    createFolder(){
      if(this.inputModal === null || this.inputModal === '') return

      const path = this.pathConvert([...this.path]) // Отдаём массив, получает массив с заменой первого эллемента, для отправки на php
      path.push(this.inputModal) // Добавляет в конец строки, название создаваемой папки

      axios.post('php/createFolder.php', { path: path.join('/') })
      .then(response => {
        this.inputModal = null
        this.showFolder({path: path.slice(0, -1).join('/')})
      })
    },
    rename(){
      const oldPath = this.pathConvert([...this.path])
      oldPath.push(this.basicName)
      const newPath = this.pathConvert([...this.path])
      newPath.push(this.inputModal)

      axios.post('php/rename.php', { oldPath: oldPath.join('/'), newPath: newPath.join('/') })
      .then(response => {
        this.inputModal = null
        this.showFolder({path: oldPath.slice(0, -1).join('/')})
      })
    },
    removeFile(){
      const item = this.selItem
      const path = this.pathConvert([...this.path])
      axios.post('php/removeFile.php', { path: item.path })
      .then(response => {
        this.showFolder({path: path.join('/')})
      })
    },
    removeFolder(){
      const item = this.selItem
      const path = this.pathConvert([...this.path])
      axios.post('php/removeFolder.php', { path: item.path })
      .then(response => {
        this.showFolder({path: path.join('/')})
      })
    },
    downloadFile(file){
      const path = this.pathConvert([...this.path])
      path.push(file)
      window.location.href = path.join('/')
    },
    search(){
      if(this.inputSearch === ''){
        this.showFolder({ path: '../folders' })
        return
      }
      axios.post('php/search.php', { search: this.inputSearch })
      .then(response => {
        const folders = []
        if(response.data.folders.length != 0)response.data.folders.forEach((item, i) =>  folders.push(Object.fromEntries(item)) ) //Заполняем массив папок

        const files = []
        if(response.data.files.length != 0)response.data.files.forEach((item, i) =>  files.push(Object.fromEntries(item)) ) //Заполняем массив папок

        this.items = []
        this.items.push(...folders)
        this.items.push(...files)

        console.log(this.items)
      })
    },

    pathConvert(path){ // Сюда приходит массив - уходит массив с заменной первого эллемента для отправки в php
      const newPath = path
      newPath.shift()
      newPath.unshift('../folders') // "Главная" => "../folders"
      return newPath
    },
    inputValid(){
      this.inputModal != null
      // Сделать валидцаию инпута !!!!!!!!!!!!!!!!!!
      this.$refs.close.setAttribute('data-bs-dismiss', 'modal')
    },
  }
})


$('#file').change(function(){
  const path = []
  $('.path').each( function(index, item) {
    path.push(item.textContent.trim())
  });
  path.shift()
  path.unshift('../folders')

  $('#frm').ajaxSubmit({
     type: 'POST',
     url: 'php/uploadFile.php',
     data: {	path: path.join('/') },
     success: function() {
        // После загрузки файла очистим форму.
        $('#frm')[0].reset()
        main.showFolder( {path: path.join('/')} )
     },
  });
})
