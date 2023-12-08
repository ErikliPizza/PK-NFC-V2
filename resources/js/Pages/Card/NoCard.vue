<template>
    <!-- Show this page if there are no cards belongs to the user or the default_card column is empty on users table -->

    <Head title="Card" />

    <div class="flex flex-col items-center justify-center p-4">
        <h1 class="text-3xl font-bold mb-4 text-center">{{__('noCard-H1')}}</h1>
        <div class="text-gray-700 text-lg text-center">{{__('noCard-Div1')}}</div>
        <div class="text-gray-700 text-sm text-center mt-2">{{__('noCard-Div2')}} <Link href="/profile" class="font-bold text-blue-500">{{__('noCard-Link')}}</Link> {{__('noCard-Div3')}}</div>
        <div class="w-52 h-52 my-16">
            <img src="/storage/gif/scanning.gif" class="object-cover"/>
        </div>
    </div>
</template>
