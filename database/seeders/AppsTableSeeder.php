<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppsTableSeeder extends Seeder
{
    /**
     * @IMPORTANT
     * 1  php artisan migrate:fresh
     * 2  php artisan db:seed --class=AppsTableSeeder
     * @IMPORTANT
     **/


    public function run()
    {
        $categories = [
            [
                'title' => 'Contact',
                'order' => 0,
                'icon' => 'contact.svg'
            ],
            [
                'title' => 'Social Media',
                'order' => 2,
                'icon' => 'social_media.svg'
            ],
            [
                'title' => 'Finance',
                'order' => 3,
                'icon' => 'finance.svg'
            ],
            [
                'title' => 'Business',
                'order' => 4,
                'icon' => 'business.svg'
            ],
        ];
        $apps = [
            [
                'component' => 'phone',
                'title' => 'Phone',
                'icon' => 'phone.png',
                'placeholder' => '1 (123) 456-7890',

                'regex' => '^(?:\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$',
                'category_id' => 1,
                'default_order' => 0
            ],
            [
                'component' => 'whatsapp',
                'title' => 'WhatsApp',
                'icon' => 'whatsapp.png',
                'placeholder' => '1 (123) 456-7890',

                'regex' => '^(?:\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$',
                'category_id' => 1,
                'default_order' => 1
            ],
            [
                'component' => 'viber',
                'title' => 'Viber',
                'icon' => 'viber.png',
                'placeholder' => '1 (123) 456-7890',

                'regex' => '^(?:\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$',
                'category_id' => 1,
                'default_order' => 2
            ],
            [
                'component' => 'web',
                'title' => 'Web',
                'icon' => 'web.png',
                'placeholder' => 'https://pikarennfc.com/card',

                'regex' => '^(?:(?:https?|ftp):\/\/)?(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?$',
                'category_id' => 1,
                'default_order' => 3
            ],
            [
                'component' => 'mail',
                'title' => 'Mail',
                'icon' => 'mail.png',
                'placeholder' => 'account@example.com',

                'regex' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$',
                'category_id' => 1,
                'default_order' => 4
            ],
            [
                'component' => 'facebook',
                'title' => 'Facebook',
                'icon' => 'facebook.png',
                'placeholder' => 'https://www.facebook.com/profile.php?id=61550099515478',

                'regex' => '^(?:(?:https?|ftp):\/\/)?(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?$',
                'category_id' => 2,                'default_order' => 5
            ],
            [
                'component' => 'instagram',
                'title' => 'Instagram',
                'icon' => 'instagram.png',
                'prefix' => 'instagram.com/',
                'placeholder' => 'struct_user',

                'regex' => '^[a-zA-Z0-9._]{1,30}$',
                'category_id' => 2,                'default_order' => 6
            ],
            [
                'component' => 'youtube',
                'title' => 'Youtube',
                'icon' => 'youtube.png',
                'placeholder' => 'https://www.youtube.com/channel/UCnJUPH5xlcKQWSxJfw1kMvQ',

                'regex' => '^(?:(?:https?|ftp):\/\/)?(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?$',
                'category_id' => 2,                'default_order' => 7
            ],
            [
                'component' => 'twitter',
                'title' => 'Twitter',
                'icon' => 'twitter.png',
                'prefix' => 'twitter.com/',
                'placeholder' => 'crazy_ivan',

                'regex' => '^[a-zA-Z0-9._]{1,30}$',
                'category_id' => 2,                'default_order' => 8
            ],
            [
                'component' => 'snapchat',
                'title' => 'Snapchat',
                'icon' => 'snapchat.png',
                'prefix' => 'snapchat.com/add/',
                'placeholder' => 'karen_scalp',

                'regex' => '^[a-zA-Z0-9._]{1,30}$',
                'category_id' => 2,                'default_order' => 9
            ],
            [
                'component' => 'tiktok',
                'title' => 'Tiktok',
                'icon' => 'tiktok.png',
                'prefix' => 'tiktok.com/@',
                'placeholder' => 'johndoe',

                'regex' => '^[a-zA-Z0-9._]{1,30}$',
                'category_id' => 2,                'default_order' => 10
            ],
            [
                'component' => 'telegram',
                'title' => 'Telegram',
                'icon' => 'telegram.png',
                'prefix' => 'telegram.me/',
                'placeholder' => 'VViglaf',

                'regex' => '^([a-zA-Z0-9_]+)$',
                'category_id' => 2,                'default_order' => 11
            ],
            [
                'component' => 'reddit',
                'title' => 'Reddit',
                'icon' => 'reddit.png',
                'prefix' => 'reddit.com/user/',
                'placeholder' => 'redhead_alien',

                'regex' => '^(?![_-])(?=.{3,20}$)(?!.*[_-]{2})[a-zA-Z0-9_-]+$',
                'category_id' => 2,                'default_order' => 12
            ],
            [
                'component' => 'pinterest',
                'title' => 'Pinterest',
                'icon' => 'pinterest.png',
                'placeholder' => 'https://pin.it/dvUemVM',

                'regex' => '^https:\/\/pin\.it\/[a-zA-Z0-9]+$|^https:\/\/www\.pinterest\.[a-zA-Z.]+\/[a-zA-Z0-9]+$',
                'category_id' => 2,                'default_order' => 13
            ],
            [
                'component' => 'twitch',
                'title' => 'Twitch',
                'icon' => 'twitch.png',
                'prefix' => 'twitch.tv/',
                'placeholder' => 'ultimate_defender',

                'regex' => '^[a-zA-Z0-9][a-zA-Z0-9_]{3,24}$',
                'category_id' => 2,                'default_order' => 14
            ],
            [
                'component' => 'bank',
                'title' => 'Enpara',
                'icon' => 'enpara.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 15
            ],
            [
                'component' => 'bank',
                'title' => 'Ziraat Bankası',
                'icon' => 'ziraat.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 16
            ],
            [
                'component' => 'bank',
                'title' => 'Garanti Bankası',
                'icon' => 'garanti.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 17
            ],
            [
                'component' => 'bank',
                'title' => 'Yapı Kredi Bankası',
                'icon' => 'yapi_kredi.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 18
            ],
            [
                'component' => 'bank',
                'title' => 'Halk Bankası',
                'icon' => 'halk.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 19
            ],
            [
                'component' => 'bank',
                'title' => 'Vakıf Bankası',
                'icon' => 'vakif.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 20
            ],
            [
                'component' => 'bank',
                'title' => 'Deniz Bankası',
                'icon' => 'deniz.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 21
            ],
            [
                'component' => 'bank',
                'title' => 'Akbank',
                'icon' => 'akbank.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 22
            ],
            [
                'component' => 'bank',
                'title' => 'İş Bankası',
                'icon' => 'is.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 23
            ],
            [
                'component' => 'bank',
                'title' => 'Paypal',
                'icon' => 'paypal.png',

                'regex' => '^[0-9]+$',
                'category_id' => 3,                'default_order' => 24
            ],
            [
                'component' => 'bank',
                'title' => 'Albaraka',
                'icon' => 'albaraka.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 25
            ],
            [
                'component' => 'bank',
                'title' => 'Kuveyt Türk',
                'icon' => 'kuveyt.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 26
            ],
            [
                'component' => 'bank',
                'title' => 'QNB Finansbank',
                'icon' => 'qnb.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 27
            ],
            [
                'component' => 'bank',
                'title' => 'ING Bank',
                'icon' => 'ing.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 28
            ],
            [
                'component' => 'bank',
                'title' => 'HSBC',
                'icon' => 'hsbc.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 29
            ],
            [
                'component' => 'bank',
                'title' => 'TEB',
                'icon' => 'teb.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 30
            ],
            [
                'component' => 'bank',
                'title' => 'Fibabanka',
                'icon' => 'fibabanka.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 31
            ],
            [
                'component' => 'bank',
                'title' => 'Vakıf Katılım',
                'icon' => 'vakif_katilim.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 33
            ],
            [
                'component' => 'bank',
                'title' => 'Şeker Bank',
                'icon' => 'seker.png',

                'regex' => '^(?:[A-Z]{2}(?:\s?\d{2})?\s?|[A-Z]{4}\s?)(?:[\dA-Z]\s?){0,30}$',
                'placeholder' => 'LU 28 001 94006447500003',
                'category_id' => 3,                'default_order' => 34
            ],
            [
                'component' => 'behance',
                'title' => 'Behance',
                'icon' => 'behance.png',
                'prefix' => 'behance.net/',
                'placeholder' => 'elonmusk',

                'regex' => '^[a-zA-Z0-9_-]{2,30}$',
                'category_id' => 4,                'default_order' => 35
            ],
            [
                'component' => 'sahibinden',
                'title' => 'Sahibinden',
                'icon' => 'sahibinden.png',

                'regex' => '^(?:(?:https?|ftp):\/\/)?(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?$',
                'category_id' => 4,                'default_order' => 36
            ],
            [
                'component' => 'googlePlay',
                'title' => 'Google Play',
                'icon' => 'google_play.png',
                'prefix' => 'play.google.com/store/apps/developer?id=',
                'placeholder' => 'NoircontactDevTeam',

                'regex' => '^[a-zA-Z0-9_-]{3,30}$',
                'category_id' => 4,                'default_order' => 37
            ],
            [
                'component' => 'appleStore',
                'title' => 'Apple Store',
                'icon' => 'apple_store.png',

                'regex' => '^(?:(?:https?|ftp):\/\/)?(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?$',
                'category_id' => 4,                'default_order' => 38
            ],
            [
                'component' => 'amazon',
                'title' => 'Amazon',
                'icon' => 'amazon.png',

                'regex' => '^(?:(?:https?|ftp):\/\/)?(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?$',
                'category_id' => 4,                'default_order' => 39
            ],
            [
                'component' => 'linkedin',
                'title' => 'Linkedin',
                'icon' => 'linkedin.png',

                'regex' => '^(?:(?:https?|ftp):\/\/)?(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?$',
                'category_id' => 4,                'default_order' => 40
            ],
        ];

        DB::table('categories')->insert($categories);

        foreach ($apps as $app) {
            DB::table('apps')->insert($app);
        }
    }
}
