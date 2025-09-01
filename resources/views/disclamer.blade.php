@extends('layouts.default')
@section('maincontent')

<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Website Disclaimer</h1>
    <p class="text-sm text-gray-600 mb-8 text-center">
        <strong>Last Updated:</strong> {{ date('F j, Y') }}
    </p>

    <!-- General Information -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">General Information</h2>
        <p class="text-gray-700 mb-4">
            The information on this website is provided on an "as is" basis. To the fullest extent permitted by law, this Company:
        </p>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>Excludes all representations and warranties relating to this website and its contents</li>
            <li>Excludes all liability for damages arising out of or in connection with your use of this website</li>
        </ul>
    </section>

    <!-- Product Information -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Product Information and Availability</h2>
        
        <h3 class="text-xl font-medium text-gray-800 mb-3">Product Descriptions</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
            <li>We strive to provide accurate product descriptions, specifications, and pricing information</li>
            <li>However, we do not warrant that product descriptions or other content is accurate, complete, reliable, current, or error-free</li>
            <li>Product images are for illustration purposes only and may not reflect the exact appearance of the actual product</li>
            <li>Colors shown are approximate and may vary depending on your display settings</li>
        </ul>

        <h3 class="text-xl font-medium text-gray-800 mb-3">Pricing and Availability</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>All prices are subject to change without notice</li>
            <li>We reserve the right to modify or discontinue products at any time without prior notice</li>
            <li>Product availability is not guaranteed and may vary</li>
            <li>In the event of a pricing error, we reserve the right to cancel any orders placed at the incorrect price</li>
        </ul>
    </section>

    <!-- User Account and Security -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">User Account and Security</h2>
        
        <h3 class="text-xl font-medium text-gray-800 mb-3">Account Responsibility</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
            <li>You are responsible for maintaining the confidentiality of your account credentials</li>
            <li>You are responsible for all activities that occur under your account</li>
            <li>We are not liable for any loss or damage arising from your failure to protect your account information</li>
        </ul>

        <h3 class="text-xl font-medium text-gray-800 mb-3">Third-Party Authentication</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>Our website may offer Google authentication services</li>
            <li>We are not responsible for the privacy practices or content of third-party authentication providers</li>
            <li>Use of third-party services is subject to their respective terms and conditions</li>
        </ul>
    </section>

    <!-- Payment Processing -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Payment Processing</h2>
        
        <h3 class="text-xl font-medium text-gray-800 mb-3">Payment Security</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
            <li>We use third-party payment processors (including PayPal) to handle transactions</li>
            <li>We do not store credit card information on our servers</li>
            <li>Payment processing is subject to the terms and conditions of the respective payment providers</li>
        </ul>

        <h3 class="text-xl font-medium text-gray-800 mb-3">Transaction Disputes</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>All sales are final unless otherwise stated in our return policy</li>
            <li>We reserve the right to refuse or cancel orders at our discretion</li>
            <li>Payment disputes should be resolved through the appropriate payment processor</li>
        </ul>
    </section>

    <!-- Limitation of Liability -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Limitation of Liability</h2>
        <p class="text-gray-700 mb-4">
            To the maximum extent permitted by law, our company shall not be liable for:
        </p>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>Any direct, indirect, incidental, special, or consequential damages</li>
            <li>Loss of profits, revenue, data, or business opportunities</li>
            <li>Damages arising from your use or inability to use our website or services</li>
            <li>Any errors, mistakes, or inaccuracies in content</li>
            <li>Unauthorized access to or alteration of your data</li>
        </ul>
    </section>

    <!-- External Links -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">External Links</h2>
        <p class="text-gray-700 mb-4">
            Our website may contain links to external websites that are not provided or maintained by us:
        </p>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>We do not guarantee the accuracy, relevance, or completeness of external content</li>
            <li>We are not responsible for the privacy practices of external websites</li>
            <li>External links do not constitute an endorsement of the linked websites</li>
        </ul>
    </section>

    <!-- Intellectual Property -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Intellectual Property</h2>
        
        <h3 class="text-xl font-medium text-gray-800 mb-3">Website Content</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
            <li>All website content, including text, graphics, logos, images, and software, is protected by copyright and other intellectual property laws</li>
            <li>Unauthorized use, reproduction, or distribution of website content is prohibited</li>
            <li>Product names and brands mentioned may be trademarks of their respective owners</li>
        </ul>

        <h3 class="text-xl font-medium text-gray-800 mb-3">User-Generated Content</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>By submitting content (reviews, comments, etc.), you grant us a non-exclusive, royalty-free license to use such content</li>
            <li>You are responsible for ensuring your submissions do not infringe on third-party rights</li>
            <li>We reserve the right to remove or modify user-generated content at our discretion</li>
        </ul>
    </section>

    <!-- Privacy and Data Protection -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Privacy and Data Protection</h2>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>Your privacy is important to us</li>
            <li>Our collection and use of personal information is governed by our Privacy Policy</li>
            <li>By using our website, you consent to the collection and use of information in accordance with our Privacy Policy</li>
        </ul>
    </section>

    <!-- Technical Disclaimer -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Technical Disclaimer</h2>
        
        <h3 class="text-xl font-medium text-gray-800 mb-3">Website Availability</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
            <li>We do not guarantee that our website will be available at all times</li>
            <li>We may suspend or restrict access for maintenance, updates, or technical issues</li>
            <li>We are not liable for any inconvenience or loss resulting from website downtime</li>
        </ul>

        <h3 class="text-xl font-medium text-gray-800 mb-3">System Compatibility</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>We do not warrant that our website will be compatible with all devices or browsers</li>
            <li>Users are responsible for ensuring their systems meet minimum requirements</li>
        </ul>
    </section>

    <!-- Jurisdiction and Governing Law -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Jurisdiction and Governing Law</h2>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>This disclaimer is governed by the laws of [Insert Your Jurisdiction]</li>
            <li>Any disputes arising from the use of this website shall be subject to the exclusive jurisdiction of the courts in [Insert Your Jurisdiction]</li>
        </ul>
    </section>

    <!-- Changes to Disclaimer -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Changes to This Disclaimer</h2>
        <p class="text-gray-700">
            We reserve the right to update or modify this disclaimer at any time without prior notice. Changes will be effective immediately upon posting on this website. Your continued use of the website after any changes constitutes acceptance of the updated disclaimer.
        </p>
    </section>

    <!-- Contact Information -->
    <section class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Contact Information</h2>
        <p class="text-gray-700 mb-4">
            If you have any questions about this disclaimer, please contact us at:
        </p>
        <div class="bg-gray-50 p-6 rounded-lg">
            <ul class="space-y-2 text-gray-700">
                <li><strong>Email:</strong> [Your Email Address]</li>
                <li><strong>Phone:</strong> [Your Phone Number]</li>
                <li><strong>Address:</strong> [Your Business Address]</li>
            </ul>
        </div>
    </section>

    <!-- Footer -->
    <div class="border-t-2 border-gray-200 pt-6 mt-8">
        <p class="text-center text-sm text-gray-600">
            <em>This disclaimer was last updated on {{ date('F j, Y') }}. Please review this page regularly to stay informed of any changes.</em>
        </p>
    </div>

    <!-- Back to Home Button -->
    <div class="text-center mt-8">
        <a href="{{ route('home') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
            Back to Home
        </a>
    </div>
</div>

@endsection