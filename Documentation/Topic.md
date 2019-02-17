# Topic

URL: https://waffensachkunde-trainer.de/Backend/Api/Topic/

## Actions

### FindById

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Topic/?action=FindById&id=1&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "topic_id": 1,
    "topic_name": "Thema 1",
    "topic_number": "1",
    "subtopics": null
}
```

### FindAll

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Topic/?action=FindAll&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "topic_id": 1,
        "topic_name": "Thema 1",
        "topic_number": "1",
        "subtopics": null
    },
    {
        "topic_id": 2,
        "topic_name": "Thema 2",
        "topic_number": "2",
        "subtopics": null
    },
    {
        "topic_id": 3,
        "topic_name": "Thema 3",
        "topic_number": "3",
        "subtopics": null
    }
]
```

### Add

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* string topic_name
* string topic_number

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Topic/?action=Add&topic_name=Thema%204&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
     "topic_id": 4,
     "topic_name": "Thema 4",
     "topic_number": "4",
     "subtopics": null
}
```

### Update

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int id
* string topic_name
* string topic_number

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Topic/?action=Update&id=4&topic_name=Erstes%20Thema&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
     "topic_id": 4,
     "topic_name": "Thema 4 edited",
     "topic_number": "4",
     "subtopics": null
}
```

### Delete

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Topic/?action=Delete&id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
     "topic_id": 4,
     "topic_name": "Thema 4",
     "topic_number": "4",
     "subtopics": null
}
```
