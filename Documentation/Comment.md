# Comment

URL: https://waffensachkunde-trainer.de/Backend/Api/Comment/

## Actions

### FindById

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Comment/?action=FindById&id=1&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "comment_id": 1,
    "comment_question_id": 1,
    "comment_user_id": 1,
    "comment_text": "Kommentar...",
    "comment_timestamp": 1544824823,
    "user": null
}
```

### FindByQuestionId

Minimale Berechtigung: APPLICATION_USERLEVEL_GUEST

Parameter

* int id (question_id)

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Comment/?action=FindByQuestionId&id=1&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "comment_id": 1,
        "comment_question_id": 1,
        "comment_user_id": 1,
        "comment_text": "Kommentar...",
        "comment_timestamp": 1544824823,
        "user": null
    },
    {
        "comment_id": 2,
        "comment_question_id": 1,
        "comment_user_id": 1,
        "comment_text": "Kommentar... 2",
        "comment_timestamp": 1544824823,
        "user": null
    },
    {
        "comment_id": 3,
        "comment_question_id": 1,
        "comment_user_id": 1,
        "comment_text": "Kommentar... 3",
        "comment_timestamp": 1544824823,
        "user": null
    }
]
```

### FindAll

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

Parameter

* string sort_by
* string sort_order
* int limit

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Comment/?action=FindAll&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "comment_id": 1,
        "comment_question_id": 1,
        "comment_user_id": 1,
        "comment_text": "Kommentar...",
        "comment_timestamp": 1544824823,
        "user": null
    },
    {
        "comment_id": 2,
        "comment_question_id": 1,
        "comment_user_id": 1,
        "comment_text": "Kommentar... 2",
        "comment_timestamp": 1544824823,
        "user": null
    },
    {
        "comment_id": 3,
        "comment_question_id": 1,
        "comment_user_id": 1,
        "comment_text": "Kommentar... 3",
        "comment_timestamp": 1544824823,
        "user": null
    }
]
```

### Add

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

Parameter

* int comment_question_id
* string comment_text

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Comment/?action=Add&topic_name=Thema%204&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "comment_id": 4,
    "comment_question_id": 1,
    "comment_user_id": 1,
    "comment_text": "Kommentar... 4",
    "comment_timestamp": 1544824823,
    "user": null
}
```

### Update

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN (oder eigener Datensatz)

Parameter

* int id
* string comment_text

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Comment/?action=Update&id=4&topic_name=Erstes%20Thema&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "comment_id": 4,
    "comment_question_id": 1,
    "comment_user_id": 1,
    "comment_text": "Kommentar... 4 Update",
    "comment_timestamp": 1544824823,
    "user": null
}
```

### Delete

Minimale Berechtigung: APPLICATION_USERLEVEL_ADMIN (oder eigener Datensatz)

Parameter

* int id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Comment/?action=Delete&id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "comment_id": 4,
    "comment_question_id": 1,
    "comment_user_id": 1,
    "comment_text": "Kommentar... 4 Update",
    "comment_timestamp": 1544824823,
    "user": null
}
```
