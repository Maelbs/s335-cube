<style>
    footer {
        background: #111;
        color: #fff;
        padding: 40px;
        text-align: center;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
    }

    a {
        color: white;
    }
</style>

<footer class="bg-gray-900 text-white py-8 mt-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex space-x-6 text-sm">

                <a href="{{ route('privacy.policy') }}"
                    class="hover:text-blue-400 transition underline decoration-blue-500"
                    style="text-decoration: underline;">
                    Politique de Protection de données et Cookies
                </a>
            </div>

            <div class="mt-4 md:mt-0 text-gray-500 text-xs">
                &copy; {{ date('Y') }} CUBE BIKES FRANCE Tous droits réservés.
            </div>
        </div>
    </div>
</footer>