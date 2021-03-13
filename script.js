new Vue({
  el: '#app',
  data: {
    mainFolders: [],
    items: [],

    path: ["Главная"],

    inputModal: null,
  },
  beforeCreate() {
    axios.post('php/showFolder.php', {path: '../folders'})
    .then(response => {
      response.data.value.forEach((item, i) => {
        this.items.push(Object.fromEntries(item))
        this.mainFolders.push(Object.fromEntries(item))
      })
      this.sortItems(this.items)//Сортировка массива(Сперва папки, после файлы)
      this.mainFolders = this.mainFolders.filter(item => item.type === "folder")//Слева должны быть только папки(Дерево каталогов)
    })
  },
  methods: {
    showFolder(item){
      if(item.type === "file") return
      console.log(item)
      axios.post('php/showFolder.php', { path: item.path })
      .then(response => {
        this.items = [] //Обнуляем массив
        response.data.value.forEach((item, i) =>  this.items.push(Object.fromEntries(item)) ) //Заполняем массив
        this.sortItems(this.items)//Сортировка массива(Сперва папки, после файлы)
      })

      this.path = ["Главная"]
      const temp = item.path.split('/')
      temp.splice(0,2)
      temp.map(item => this.path.push(item))
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
    uploadFile(){
      const path = this.pathConvert([...this.path]) // Отдаём массив, получает строку с заменой первого эллемента, для отправки на php
      console.log(path.join('/'))

      // axios.post('php/uploadFile.php', { path: path.join('/') })
      // .then(response => {
      //   this.inputModal = null
      //   this.showFolder({path: path.slice(0, -1).join('/')})
      // })
    },
    pathConvert(path){ // Сюда приходит массив - уходит массив с заменной первого эллемента для отправки в php
      const newPath = path
      newPath.shift()
      newPath.unshift('../folders') // "Главная" => "../folders"
      return newPath
    }
  }
})
