function rmPost(
    endpoint,
    data
) {
    fetch(
        rmTracker.apiUrl +
        endpoint,
        {
            method: 'POST',
            headers: {
                'Content-Type':
                    'application/json'
            },
            body:
                JSON.stringify(
                    data
                )
        }
    );
}

let rmStartTime = Date.now();

window.addEventListener(
    'beforeunload',
    function () {

        const seconds =
            Math.round(
                (
                    Date.now()
                    -
                    rmStartTime
                ) / 1000
            );
        rmPost(
            '/page-time',
            {
                page:
                    window
                        .location
                        .pathname,
                seconds:
                    seconds
            }
        );


    }
);

setInterval(
    function () {

        rmPost(
            '/heartbeat',
            {}
        );

    },
    30000
);

document.addEventListener(
    'click',
    function (e) {

        /*
        --------------------------------------
        Eventos genéricos
        --------------------------------------
        */
        const element =
            e.target.closest(
                '.rm-track-event'
            );

        if (element) {

            rmPost(
                '/event',
                {
                    type:
                        element.dataset.rmType ||
                        'click',

                    resource:
                        element.dataset.rmResource ||
                        ''
                }
            );

            return;
        }

        /*
        --------------------------------------
        Compatibilidad con PDFs
        --------------------------------------
        */
        const link =
            e.target.closest('a');

        if (!link) {
            return;
        }

        const href =
            link.href || '';

        if (
            href.includes(
                'drive.google.com'
            ) ||
            href
                .toLowerCase()
                .endsWith('.pdf')
        ) {

            rmPost(
                '/event',
                {
                    type: 'click',
                    resource: href
                }
            );
        }
    }
);

const rmActiveUsers =
    document.getElementById(
        'rm-active-users'
    );

const rmTodayVisits =
    document.getElementById(
        'rm-today-visits'
    );

const rmCountries =
    document.getElementById(
        'rm-countries-list'
    );

if (
    rmActiveUsers ||
    rmTodayVisits ||
    rmCountries
) {

    setInterval(
        async function () {

            try {

                const response =
                    await fetch(
                        rmTracker.apiUrl +
                        '/dashboard-realtime',
                        {
                            credentials:
                                'same-origin',

                            headers: {
                                'X-WP-Nonce':
                                    rmTracker.nonce
                            }
                        }
                    );

                if (
                    !response.ok
                ) {
                    return;
                }

                const data =
                    await response.json();

                if (
                    rmActiveUsers
                ) {
                    rmActiveUsers.textContent =
                        data.active_users;
                }

                if (
                    rmTodayVisits
                ) {
                    rmTodayVisits.textContent =
                        data.today_visits;
                }

                if (
                    rmCountries &&
                    Array.isArray(
                        data.countries
                    )
                ) {

                    let html = '';

                    data.countries.forEach(
                        function (
                            country
                        ) {

                            html += `
                                <li>
                                    ${country.pais}
                                    -
                                    ${country.total}
                                </li>
                            `;
                        }
                    );

                    rmCountries.innerHTML =
                        html;
                }

            } catch (e) {
                console.error(
                    e
                );
            }

        },
        15000
    );

}