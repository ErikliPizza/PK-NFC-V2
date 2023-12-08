@extends('admin.layouts.app')

@section('content')

    <div class="p-3">
        <span class="px-1">Serializer</span>
        <form action="{{ route('admin.key.decrypt') }}" method="POST" class="mb-4">
            @csrf
            <div class="flex justify-center items-center space-x-2">
                <input type="text" name="identity" id="identity" class="w-full px-3 py-2 border rounded-lg">
                <button type="submit" class="bg-gray-700 p-1 rounded-md text-sm text-white">SEND</button>
            </div>
        </form>
        @if ($message = Session::get('key'))
            <strong>Generated full key: </strong>
        <hr class="p-2">
            <p id="messagePara" class="text-center text-sm text-green-600 break-words" onclick="copyToClipboard()">
                {{ $message }}
            </p>
        <hr class="p-2">
            <button class="w-full p-1 rounded-md bg-blue-950 text-white text-sm text-center" onclick="copyToClipboard()">Copy</button>
        @endif
        <script>
            function copyToClipboard() {
                // Create a temporary textarea element
                const textarea = document.createElement('textarea');
                // Get the text from your paragraph
                textarea.value = document.getElementById('messagePara').innerText;
                // Append it to the body
                document.body.appendChild(textarea);
                // Select the text
                textarea.select();
                // Execute the "Copy" command
                document.execCommand('copy');
                // Remove the textarea element
                document.body.removeChild(textarea);

                // Optional: Alert the user that the text was copied
                alert('Copied to clipboard');
            }
        </script>
    </div>

        <h1 class="text-center text-3xl font-bold m-4 text-gray-600"> Please Read The Specified Fields Before Creating a New Category and App </h1>
    <div class="max-w-3xl mx-auto p-4">
        <!-- Creating a New Category -->
        <div class="bg-white shadow-md rounded p-6 mb-4">
            <h1 class="text-2xl font-semibold mb-4">Creating a New Category</h1>
            <p><strong>Title:</strong> The title for the category</p>
            <p><strong>Order:</strong> The default order for the category, it represents the rendering order on the /nfc/slug and /card pages.</p>
            <p><strong>Image:</strong> It should be a visual that does not break away from the context</p>
            <h2 class="text-lg font-semibold mt-4">What to do after creating a new category?</h2>
            <p>Go to localization JSON files on the project (/lang/ar, en, tr) and add the category name to all of these files.</p>
        </div>

        <!-- Creating a New App -->
        <div class="bg-white shadow-md rounded p-6 mb-4">
            <h1 class="text-2xl font-semibold mb-4">Creating a New App</h1>
            <p>Creating a new app may be a hard and complex process, especially if you have a custom component.</p>
            <p><strong>Order:</strong> The default order for the app, it represents the rendering order of the added information related to this app. Users may change the order of their information after the insertion process.</p>
            <p><strong>Title:</strong> The title of the app</p>
            <p><strong>Placeholder:</strong> The placeholder of the app</p>
            <p><strong>Prefix:</strong> This is a prefix to make things easier. For example, if you want to add Instagram as an app, you can use "instagram.com/" for the prefix, so users will type only their username as: username, and it will look like this: "prefix+username," in this specific example, "instagram.com/username."</p>
            <p><strong>Regex:</strong> This is a validation rule for the front and back-end sections. This is an advanced option for experienced developers to validate data on the front-end and back-end sections.</p>
            <p><strong>Suffix:</strong> Same as prefix. You can use a suffix, for example, as "userinput-@gmail.com".</p>
            <p><strong>Category:</strong> Select a category here.</p>
            <p><strong>Image:</strong> It should be a visual that does not break away from the context</p>
            <p><strong>Component:</strong> This is critical. Here, you are defining the component name that you want to fetch from the resources/js/components/Apps. If there are no components with the specified component name, it will use Default.vue component. You need to create a component inside this location if you have custom logic.</p>

            <h2 class="text-lg font-semibold mt-4">What to do after creating a new app?</h2>
            <p>Go to localization JSON files on the project (/lang/ar, en, tr) and add the app name to all of these files.</p>
            <p>Create your custom component if you need.</p>

            <!-- What does a component do? -->
            <h2 class="text-lg font-semibold mt-4">What does a component do?</h2>
            <p>A "component" is responsible for the triggered action when a user clicks on the information record on the /nfc/slug page. It can perform actions such as copying the value, opening WhatsApp, redirecting to Instagram, etc.</p>

            <!-- What does a component do? -->
            <h2 class="text-lg font-semibold mt-4">How to create and assign a component?</h2>
            <p>Create your component inside the resources/js/Components/Apps directory. Check other components for detailed reference and to see how it works.</p>
            <p>Assign your component inside the resources/js/Pages/Nfc/Partials/CategoryAndAppSection.vue file.</p>
        </div>
    </div>


@endsection
