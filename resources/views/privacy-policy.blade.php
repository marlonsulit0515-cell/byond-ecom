@extends('layouts.default')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Privacy Policy</h1>
        
        <p class="text-sm text-gray-600 mb-8 text-center">
            <strong>Last Updated:</strong> {{ date('F j, Y') }}
        </p>

        <!-- Introduction -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Introduction</h2>
            <p class="text-gray-700 mb-4">
                This Privacy Policy describes how we collect, use, and protect your information when you visit our website, 
                make purchases, or interact with our services. We are committed to protecting your privacy and ensuring 
                the security of your personal information.
            </p>
            <p class="text-gray-700">
                By using our website, you agree to the collection and use of information in accordance with this policy.
            </p>
        </section>

        <!-- Information We Collect -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Information We Collect</h2>
            
            <h3 class="text-xl font-medium text-gray-800 mb-3">Personal Information</h3>
            <p class="text-gray-700 mb-4">
                We may collect the following personal information when you:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
                <li><strong>Create an account:</strong> Name, email address, password</li>
                <li><strong>Make a purchase:</strong> Billing address, shipping address, phone number</li>
                <li><strong>Contact us:</strong> Name, email address, message content</li>
                <li><strong>Subscribe to newsletters:</strong> Email address, preferences</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">Third-Party Authentication</h3>
            <p class="text-gray-700 mb-4">
                When you use Google Sign-In, we may collect:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
                <li>Name and email address from your Google account</li>
                <li>Profile picture (if publicly available)</li>
                <li>Google account ID for authentication purposes</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">Automatically Collected Information</h3>
            <p class="text-gray-700 mb-4">
                We automatically collect certain information when you visit our website:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
                <li>IP address and location information</li>
                <li>Browser type and version</li>
                <li>Operating system</li>
                <li>Pages visited and time spent on pages</li>
                <li>Referring website addresses</li>
                <li>Device information (mobile, tablet, desktop)</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">Cookies and Similar Technologies</h3>
            <p class="text-gray-700 mb-4">
                We use cookies and similar technologies to:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
                <li>Remember your login information and preferences</li>
                <li>Keep items in your shopping cart</li>
                <li>Analyze website usage and performance</li>
                <li>Provide personalized content and advertisements</li>
                <li>Ensure website security and prevent fraud</li>
            </ul>
        </section>

        <!-- How We Use Information -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">How We Use Your Information</h2>
            <p class="text-gray-700 mb-4">
                We use the collected information for the following purposes:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
                <li><strong>Order Processing:</strong> Process and fulfill your orders, handle payments, and provide customer support</li>
                <li><strong>Account Management:</strong> Create and maintain your user account, authenticate logins</li>
                <li><strong>Communication:</strong> Send order confirmations, shipping updates, and customer service messages</li>
                <li><strong>Marketing:</strong> Send newsletters, promotional emails, and special offers (with your consent)</li>
                <li><strong>Website Improvement:</strong> Analyze usage patterns to improve our website and services</li>
                <li><strong>Security:</strong> Detect and prevent fraud, unauthorized access, and other security issues</li>
                <li><strong>Legal Compliance:</strong> Comply with applicable laws and regulations</li>
            </ul>
        </section>

        <!-- Information Sharing -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">How We Share Your Information</h2>
            <p class="text-gray-700 mb-4">
                We do not sell, trade, or rent your personal information to third parties. We may share your information in the following circumstances:
            </p>

            <h3 class="text-xl font-medium text-gray-800 mb-3">Service Providers</h3>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
                <li><strong>Payment Processors:</strong> PayPal and other payment providers to process transactions</li>
                <li><strong>Shipping Companies:</strong> Delivery services to fulfill your orders</li>
                <li><strong>Email Services:</strong> Email service providers for communications</li>
                <li><strong>Analytics:</strong> Website analytics services to improve our site</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">Legal Requirements</h3>
            <p class="text-gray-700 mb-4">
                We may disclose your information if required by law or in response to:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
                <li>Court orders, subpoenas, or legal processes</li>
                <li>Requests from law enforcement agencies</li>
                <li>Protection of our rights, property, or safety</li>
                <li>Prevention of fraud or illegal activities</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">Business Transfers</h3>
            <p class="text-gray-700">
                In the event of a merger, acquisition, or sale of business assets, your information may be transferred 
                to the new entity, subject to the same privacy protections.
            </p>
        </section>

        <!-- Data Security -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Data Security</h2>
            <p class="text-gray-700 mb-4">
                We implement appropriate security measures to protect your personal information:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
                <li>SSL encryption for data transmission</li>
                <li>Secure servers and databases</li>
                <li>Regular security updates and monitoring</li>
                <li>Access controls and authentication</li>
                <li>Employee training on data protection</li>
            </ul>
            <p class="text-gray-700">
                However, no method of transmission over the Internet or electronic storage is 100% secure. 
                While we strive to use commercially acceptable means to protect your information, we cannot 
                guarantee absolute security.
            </p>
        </section>

        <!-- Data Retention -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Data Retention</h2>
            <p class="text-gray-700 mb-4">
                We retain your personal information for as long as necessary to:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
                <li>Provide our services and fulfill transactions</li>
                <li>Comply with legal obligations</li>
                <li>Resolve disputes and enforce agreements</li>
                <li>Improve our services and customer experience</li>
            </ul>
            <p class="text-gray-700">
                Account information is retained until you request deletion or your account is inactive for an extended period. 
                Order history may be retained longer for business and legal purposes.
            </p>
        </section>

        <!-- Your Rights -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Your Rights and Choices</h2>
            <p class="text-gray-700 mb-4">
                You have the following rights regarding your personal information:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-6">
                <li><strong>Access:</strong> Request a copy of the personal information we have about you</li>
                <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information</li>
                <li><strong>Deletion:</strong> Request deletion of your personal information (subject to legal requirements)</li>
                <li><strong>Portability:</strong> Request transfer of your data to another service provider</li>
                <li><strong>Opt-out:</strong> Unsubscribe from marketing communications at any time</li>
                <li><strong>Cookie Control:</strong> Manage cookie preferences through your browser settings</li>
            </ul>

            <h3 class="text-xl font-medium text-gray-800 mb-3">How to Exercise Your Rights</h3>
            <p class="text-gray-700 mb-4">
                To exercise any of these rights, please contact us using the information provided below. 
                We may need to verify your identity before processing your request.
            </p>
        </section>

        <!-- Third-Party Services -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Third-Party Services</h2>
            
            <h3 class="text-xl font-medium text-gray-800 mb-3">Google Services</h3>
            <p class="text-gray-700 mb-4">
                Our website integrates with Google services for authentication. Please review Google's Privacy Policy 
                to understand how they collect and use your information.
            </p>

            <h3 class="text-xl font-medium text-gray-800 mb-3">PayPal</h3>
            <p class="text-gray-700 mb-4">
                We use PayPal for payment processing. PayPal's collection and use of your information is governed 
                by their Privacy Policy.
            </p>

            <h3 class="text-xl font-medium text-gray-800 mb-3">External Links</h3>
            <p class="text-gray-700">
                Our website may contain links to third-party websites. We are not responsible for the privacy 
                practices of these external sites and encourage you to review their privacy policies.
            </p>
        </section>

        <!-- Children's Privacy -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Children's Privacy</h2>
            <p class="text-gray-700">
                Our website is not intended for children under the age of 13. We do not knowingly collect personal 
                information from children under 13. If you are a parent or guardian and believe your child has 
                provided us with personal information, please contact us immediately so we can delete such information.
            </p>
        </section>

        <!-- International Users -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">International Users</h2>
            <p class="text-gray-700">
                If you are accessing our website from outside [Your Country], please be aware that your information 
                may be transferred to, stored, and processed in [Your Country]. By using our website, you consent 
                to the transfer of your information to our facilities and those third parties with whom we share 
                it as described in this Privacy Policy.
            </p>
        </section>

        <!-- Changes to Privacy Policy -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Changes to This Privacy Policy</h2>
            <p class="text-gray-700 mb-4">
                We may update this Privacy Policy from time to time to reflect changes in our practices or legal requirements. 
                We will notify you of any material changes by:
            </p>
            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2 mb-4">
                <li>Posting the updated policy on our website</li>
                <li>Updating the "Last Updated" date</li>
                <li>Sending email notifications for significant changes (if applicable)</li>
            </ul>
            <p class="text-gray-700">
                Your continued use of our website after any changes constitutes acceptance of the updated Privacy Policy.
            </p>
        </section>

        <!-- Contact Information -->
        <section class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b-2 border-gray-200 pb-2">Contact Us</h2>
            <p class="text-gray-700 mb-4">
                If you have any questions about this Privacy Policy or our data practices, please contact us:
            </p>
            <div class="bg-gray-50 p-6 rounded-lg">
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Email:</strong> privacy@[yourdomain.com]</li>
                    <li><strong>Phone:</strong> [Your Phone Number]</li>
                    <li><strong>Address:</strong> [Your Business Address]</li>
                    <li><strong>Privacy Officer:</strong> [Privacy Officer Name] (if applicable)</li>
                </ul>
            </div>
            <p class="text-gray-700 mt-4 text-sm">
                We will respond to your privacy-related inquiries within 30 days.
            </p>
        </section>

        <!-- Footer -->
        <div class="border-t-2 border-gray-200 pt-6 mt-8">
            <p class="text-center text-sm text-gray-600">
                <em>This Privacy Policy was last updated on {{ date('F j, Y') }}. Please review this page regularly to stay informed of any changes.</em>
            </p>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-center space-x-4 mt-8">
            <a href="{{ route('home') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                Back to Home
            </a>

        </div>
    </div>
</div>
@endsection