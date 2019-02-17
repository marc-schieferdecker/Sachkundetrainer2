# Favourite

URL: https://waffensachkunde-trainer.de/Backend/Api/Favourite/

## Actions

### FindAll

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Favourite/?action=FindAll&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
[
    {
        "favourite_id": 1,
        "user_id": 1,
        "question_id": 1
    },
    {
        "favourite_id": 2,
        "user_id": 1,
        "question_id": 2
    },
    {
        "favourite_id": 3,
        "user_id": 1,
        "question_id": 3
    }
]
```

### Add

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

Parameter

* int question_id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Favourite/?action=Add&question_id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "favourite_id": 4,
    "user_id": 1,
    "question_id": 4
}
```

### Remove

Minimale Berechtigung: APPLICATION_USERLEVEL_USER

Parameter

* int question_id

[Beispiel](https://waffensachkunde-trainer.de/Backend/Api/Favourite/?action=Remove&question_id=4&api_key=c0aa6c85ab5d92513398a28381c701e6)

**Response**

```json
{
    "favourite_id": 4,
    "user_id": 1,
    "question_id": 4
}
```
