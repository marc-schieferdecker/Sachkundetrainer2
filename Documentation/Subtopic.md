# Subtopic

URL: https://waffensachkunde-trainer.de/Backend/Api/Subtopic/

## Actions

### FindById

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Subtopic/?action=FindById&id=1&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "subtopic_id": 1,
    "subtopic_name": "Unterthema 1",
    "subtopic_number": "1",
    "subtopic_topic_parent_id": 1
}
```

### FindAll

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Subtopic/?action=FindAll&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "subtopic_id": 1,
        "subtopic_name": "Unterthema 1",
        "subtopic_number": "1",
        "subtopic_topic_parent_id": 1
    },
    {
        "subtopic_id": 2,
        "subtopic_name": "Unterthema 2",
        "subtopic_number": "2",
        "subtopic_topic_parent_id": 1
    },
    {
        "subtopic_id": 2,
        "subtopic_name": "Unterthema 3",
        "subtopic_number": "3",
        "subtopic_topic_parent_id": 1
    }
]
```

### Add

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* string subtopic_name
* string subtopic_number
* int subtopic_topic_parent_id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Subtopic/?action=Add&subtopic_name=Thema%204&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
     "subtopic_id": 4,
     "subtopic_name": "Unterthema 4",
     "subtopic_number": "4",
     "subtopic_topic_parent_id": 1
}
```

### Update

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int id
* string subtopic_name
* string subtopic_number
* int subtopic_topic_parent_id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Subtopic/?action=Update&id=4&subtopic_name=Erstes%20Thema&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
     "subtopic_id": 4,
     "subtopic_name": "Unterthema 4 edited",
     "subtopic_number": "4",
     "subtopic_topic_parent_id": 1
}
```

### Delete

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Subtopic/?action=Delete&id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
     "subtopic_id": 4,
     "subtopic_name": "Unterthema 4 edited",
     "subtopic_number": "4",
     "subtopic_topic_parent_id": 1
}
```
