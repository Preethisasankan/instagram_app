Instagram API
Before You Start :
https://developers.facebook.com/docs/instagram-basic-display-api/getting-started

Step 1: Create a Facebook App.

Step 2: Configure Instagram Basic Display.
Step 3: Add an Instagram Test User .
Step 4: Authenticate the Test User (code).

Requirements
----------------------
In order to use this API, you must undergo App Review and request approval for:

the Instagram Public Content Access feature
the instagram_basic permission

Limitations 
------------------------
You can query a maximum of 30 unique hashtags on behalf of an Instagram Business or Creator Account within a rolling, 7 day period. Once you query a hashtag, it will count against this limit for 7 days. Subsequent queries on the same hashtag within this time frame will not count against your limit, and will not reset its initial query 7 day timer.
Personally identifiable information will not be included in responses.
You cannot comment on hashtagged media objects discovered through the API.
Hashtags on Stories are not supported.
Emojis in hashtag queries are not supported.
The API will return a generic error for any requests that include hashtags that we have deemed sensitive or offensive.



