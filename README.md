Server APIs
===

获取文章列表
---
GET `/jddj/v1/posts/?category=:slug`

Response Body: 
```json
{
  "id":"",
  "title":"",
  "excerpt":"",
  "content":"",
  "status":"",
  "slug":"",
  "posterUrl":"",
  "categories":[""],
  "town":"",
  "date":"2018-01-01", // 仅当categories包含"月度菜单"时有此属性
  "author":{
    "id":"",
    "name":"",
    "roles":[]
  },
  "createdAt":"",
  "updatedAt":""
}
```

获取附件列表
---
GET `/jddj/v1/attachments/?category=:slug`

Response Body: 
```json
{
  "id":"",
  "title":"",
  "type":"image|video|audio",
  "mime":"image/jpeg|video/mp4|audio/mp3...",
  "url":"",
  "categories":[""],
  "createdAt":"",
  "updatedAt":""
}
```

获取党史上的这一天
---
GET `/jddj/v1/cpc-review/:dateType/:mm-:dd`

Response Body: 
```json
{
  "content":"<h3>...</h3><p>...</p>..."
}
```

获取纪念日人数和占比
---
GET `jddj/v1/user-count/:dateType/:mm/:dd`

`:dateType`: birth 生日 | enroll 入党日 | memo 纪念日

`:mm`: 月份
`:dd`: 日期

Response Body: 
```json
{
  "count":0,
  "percentage":10
}
```

获取党建声音列表
---
GET `jddj/v1/speeches/:speechType?page=:page&limit=:limit`

Response Body: 
```json
[{
	"id":"",
	"type":"movie|talk",
	"bgid":"",
	"audioUrl":"",
	"authorName":""
}]
```

上传党建声音
---
POST `jddj/v1/speeches/:speechType`

- `:speechType`: movie|talk

Request Body: (form-data)
- type
- bgid
- audio

Response Body: 
```json
{
	"id":"",
	"bgid":"",
	"audioUrl":"",
	"qrcodeUrl":""
}
```

获得党建声音
---
GET `jddj/v1/speeches/:id`

Response Body: 
```json
{
	"id":"",
	"type":"movie|talk",
	"bgid":"",
	"audioUrl":"",
	"authorName":""
}
```

上传座右铭
---
POST `jddj/v1/mottoes`

Request Body: (form-data)
- text
- image
- authorName

Response Body: 
```json
{
	"id":"",
	"text":"",
	"imageUrl":"",
	"authorName":"",
	"qrcodeUrl":""
}
```

获得座右铭
---
GET `jddj/v1/mottoes/:id`

Response Body: 
```json
{
	"id":"",
	"text":"",
	"imageUrl":"",
	"authorName":""
}
```

获得座右铭列表
---
GET `jddj/v1/mottoes?page=:page&limit=:limit`

Response Body: 
```json
[{
	"id":"",
	"text":"",
	"imageUrl":"",
	"authorName":""
}]
```

下载党建地图信息
---
GET `jddj/v1/spots`

Response Body: 
```json
{
	"id":"",
	"type":"服务中心|服务站|党性教育基地|组织生活现场开放点",
	"name":"",
	"town":"",
	"address":"",
	"latitude":31.000000,
	"longitude":121.000000,
	"contact":"",
	"phone":"",
	"wechatPublicName":"",
	"desc":"<p>...</p>...",
	"imageUrls": [""],
	"liveVideoUrl":""
}
```
