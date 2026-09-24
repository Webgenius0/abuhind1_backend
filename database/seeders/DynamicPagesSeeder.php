<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DynamicPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'page_title'   => 'FAQ',
                'page_slug'    => Str::slug('FAQ'),
                'page_content' => '
                    <h2>Frequently Asked Questions</h2>

                    <h3>1. What is this service?</h3>
                    <p>This service is designed to provide users with a simple, convenient, and reliable experience.</p>

                    <h3>2. How can I get started?</h3>
                    <p>Getting started is easy. Simply follow the instructions provided and complete the required steps.</p>

                    <h3>3. Is registration required?</h3>
                    <p>Registration may be required to access certain features and services.</p>

                    <h3>4. How long does it take to receive a response?</h3>
                    <p>Response times may vary depending on the type of request. We aim to respond as quickly as possible.</p>

                    <h3>5. Can I update my information later?</h3>
                    <p>Yes, you can update your information whenever necessary, depending on the available options.</p>

                    <h3>6. What should I do if I face a problem?</h3>
                    <p>If you experience any issues, please contact our support team for assistance.</p>

                    <h3>7. Is my information secure?</h3>
                    <p>We take appropriate measures to protect your information and maintain your privacy.</p>

                    <h3>8. How can I contact support?</h3>
                    <p>You can contact our support team through the available contact options on the website or platform.</p>
                ',
                'status'       => 'active',
            ],

            [
                'page_title'   => 'Terms & Conditions',
                'page_slug'    => Str::slug('Terms & Conditions'),
                'page_content' => '
                    <h2>Terms & Conditions</h2>

                    <p>By accessing or using this website and its services, you agree to comply with these Terms & Conditions.</p>

                    <h3>1. Use of the Service</h3>
                    <p>You agree to use our services only for lawful purposes and in accordance with these terms.</p>

                    <h3>2. User Responsibilities</h3>
                    <p>Users are responsible for providing accurate information and maintaining the security of their account.</p>

                    <h3>3. Prohibited Activities</h3>
                    <p>You must not use the service for any illegal, fraudulent, abusive, or unauthorized activity.</p>

                    <h3>4. Changes to the Terms</h3>
                    <p>We may update these Terms & Conditions from time to time. Any changes will be published on this page.</p>

                    <h3>5. Contact</h3>
                    <p>If you have any questions regarding these terms, please contact our support team.</p>
                ',
                'status'       => 'active',
            ],

            [
                'page_title'   => 'Privacy Policy',
                'page_slug'    => Str::slug('Privacy Policy'),
                'page_content' => '
                    <h2>Privacy Policy</h2>

                    <p>We respect your privacy and are committed to protecting your personal information.</p>

                    <h3>1. Information We Collect</h3>
                    <p>We may collect information that you provide directly, such as your name, email address, contact information, and other required details.</p>

                    <h3>2. How We Use Your Information</h3>
                    <p>Your information may be used to provide services, improve user experience, communicate with you, and maintain the security of our platform.</p>

                    <h3>3. Information Security</h3>
                    <p>We take reasonable measures to protect your personal information from unauthorized access, use, or disclosure.</p>

                    <h3>4. Third-Party Services</h3>
                    <p>We may use trusted third-party services when necessary to operate and improve our services.</p>

                    <h3>5. Policy Updates</h3>
                    <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page.</p>

                    <h3>6. Contact Us</h3>
                    <p>If you have any questions about this Privacy Policy, please contact our support team.</p>
                ',
                'status'       => 'active',
            ],
        ];

        DB::table('dynamic_pages')->insert($pages);
    }
}
