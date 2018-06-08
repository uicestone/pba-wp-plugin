Server APIs
===

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
GET `jddj/v1/speeches/:speechType`

Response Body: 
```json
[{
	"id":"",
	"type":"movie|talk",
	"bgid":"",
	"audioUrl":""
}]
```

上传党建声音
---
POST `jddj/v1/speeches/:speechType`

Request Body: (form-data)
- type
- bgid
- audio

Response Body: 
```json
{
	"id":"",
	"type":"movie|talk",
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
	"audioUrl":""

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
	"authorName":""
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
GET `jddj/v1/mottoes`

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
	"liveVideo":false
}
```
