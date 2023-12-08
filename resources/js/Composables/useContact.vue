<script>
// Import the useClipboard composable
import { useClipboard } from "@/Composables/useClipboard.vue";

// Destructure the copyToClipboard function from the useClipboard composable
const { copyToClipboard } = useClipboard();

// Define a function that adds a contact
export function useContact() {
    // Function to add a contact based on user agent
    const callNumber = (value) => {
        // Check if the user agent contains "Mobi" indicating a mobile device
        const isMobile = /Mobi|Android/i.test(navigator.userAgent);

        if (isMobile) {
            // Construct the URL with the contact number
            const contactUrl = `tel:${value}`;
            // Open the default contact application
            window.open(contactUrl, '_blank');
        } else {
            // Copy the value to clipboard
            copyToClipboard(value, value);
        }
    };

  // This function generates a VCard (contact card) in the form of a .vcf file and triggers its download.
  const addContact = (title, numbers = [], mails = [], address = "", webs = []) => {
    let emailField = "";
    let telField = "";
    let webField = "";

    // Check if there are any email addresses, and if so, construct the email field.
    if (mails.length > 0) {
      emailField = mails.map(mail => `EMAIL;TYPE=${mail.title}:${mail.value}\r\n`).join("\r\n");
    }

    // Check if there are any phone numbers, and if so, construct the phone number field.
    if (numbers.length > 0) {
      telField = numbers.map(number => `TEL;TYPE=${number.title}:${number.value}\r\n`).join("\r\n");
    }

    // Check if there are any website URLs, and if so, construct the website field.
    if (webs.length > 0) {
      webField = webs.map(web => `URL:${web}\r\n`).join("\r\n");
    }
      // Create a new URL object based on the current window location
      const currentURL = new URL(window.location.href);
      // Remove any query parameters from the URL
      currentURL.search = '';
    // Create the VCard data string.
    const vCardData = `BEGIN:VCARD\r\n
VERSION:3.0\r\n
FN:${title}\r\n
${telField}
${emailField}
${webField}
URL:${currentURL.href}\r\n
ADR;TYPE=OTHER:${address}\r\n
END:VCARD`;

    // Create a Blob containing the VCard data.
    const blob = new Blob([vCardData], { type: 'text/vcard' });

    // Create a URL for the Blob.
    const url = window.URL.createObjectURL(blob);

    // Create a hidden 'a' element for downloading the VCard.
    const a = document.createElement('a');
    a.style.display = 'none';
    a.href = url;
    a.setAttribute('download', title + '.vcf');

    // Append the 'a' element to the document body.
    document.body.appendChild(a);

    // Trigger a click event on the 'a' element to initiate the download.
    a.click();

    // Revoke the URL to release resources.
    window.URL.revokeObjectURL(url);
  };


    // Return the addToContact function
    return { callNumber, addContact };
}
</script>

