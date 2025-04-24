# 📁 Laravel File Upload + Sharing API

A backend-only Laravel API for uploading and sharing files with a secure, time-limited download link. Inspired by WeTransfer, this system supports multi-file uploads, password protection, and auto-expiry.

---

# Features

- Upload multiple files (max 5)
- Generate secure download links
- Expire uploads after X days (default: 1)
- Optional email notification (queued)
- Optional password-protected downloads
- File stats (downloads, size, expiry)
- Artisan cleanup command to delete expired files


---

#  Tech Stack

- Laravel 10+
- Laravel Storage API
- Artisan Scheduler
- Optional: Laravel Sanctum (for future auth)

---

# Post collection 

Wetransfer.postman_collection.json

# Setup Instructions

1. ** Clone repo & install dependencies **


cd wetransfer
composer install

Then in config/filesystems.php, add:

'uploads' => [
    'driver' => 'local',
    'root' => storage_path('app/uploads'),
],

 php artisan migrate

 API Documentation
 Upload Files

 POST /api/upload

Form Data:

Field	Type		Description
files[]		            Up to 5 files (max 100MB each)
expires_in	            optional integer	Days until expiry (default: 1, max: 30)
email_to_notify:-	    optional string	Email to notify with download link
password	            string	require password for protected download
password_confirmation   string	require password for protected download


Reponse:-
{
  "success": true,
  "download_link": "https://baseurl/api/download/{token}"
}


POST /api/download/{token}

payload
{
  "password": "secret123"
}

Response:
Streams a ZIP of uploaded files

Increments download count

Upload Stats
GET /api/uploads/stats/{token}

Response

{

    "data": {

        "Expires_day": "2025-04-28 16:07:06",
        "Number_of_files": 4,
        "TotalSize": 7492248,
        "TotalDownloads": 4,

        "files": [

            {
                "File_name": "Certificate of completion for certificate.pdf",
                "File_size": 3736379,
                "Downloads": 1
            },
            {
                "File_name": "Distorting Reality.docx",
                "File_size": 9745,
                "Downloads": 1
            },
            {
                "File_name": "Certificate of completion for certificate.pdf",
                "File_size": 3736379,
                "Downloads": 1
            },
            {
                "File_name": "Distorting Reality.docx",
                "File_size": 9745,
                "Downloads": 1
            }

        ]

    }
}

Auto-Delete Expired Uploads

Command:
php artisan clean:expired-uploads

mail sending using queue 
Command
php artisan queue:work 

Post collection 
Wetransfer.postman_collection.json
