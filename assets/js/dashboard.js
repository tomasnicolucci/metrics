console.log(rmDashboard);
console.log('rmTracker:', rmTracker);

let visitsChart = null;
const ctx =
    document.getElementById(
        'rm-visits-chart'
    );

if (
    ctx &&
    rmDashboard?.visits
) {

    const labels =
        rmDashboard.visits
            .map(
                item => item.dia
            );

    const data =
        rmDashboard.visits
            .map(
                item =>
                    Number(
                        item.total
                    )
            );

    visitsChart =
        new Chart(
            ctx,
            {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Visitas',
                            data
                        }
                    ]
                }
            }
        );
}

const period =
    document.getElementById(
        'rm-period'
    );

if (period) {

    period.addEventListener(
        'change',
        loadDashboard
    );

}

async function loadDashboard()
{
    const value =
        document
            .getElementById(
                'rm-period'
            )
            .value;

    const response =
        await fetch(
            rmTracker.apiUrl +
            '/dashboard-data?period=' +
            value,
            {
                credentials: 'same-origin',
                headers: {
                    'X-WP-Nonce':
                        rmTracker.nonce
                }
            }
        );
    const data =
        await response.json();

    console.log(data);
    
    
    document.getElementById(
        'rm-today-visits'
    ).textContent =
        data.summary.visitas_hoy;

    document.getElementById(
        'rm-unique-users'
    ).textContent =
        data.summary.usuarios_unicos;

    document.getElementById(
        'rm-pdf-opens'
    ).textContent =
        data.summary.pdf_opens;

    document.getElementById(
        'rm-active-users'
    ).textContent =
        data.summary.visitantes_activos;

    const countries =
        document.getElementById(
            'rm-countries-list'
        );

    if (
        countries &&
        data.countries
    ) {

        countries.innerHTML =
            data.countries
                .map(
                    country =>
                        `
                        <li>
                            ${country.pais}
                            -
                            ${country.total}
                        </li>
                        `
                )
                .join('');
    }

    const sources =
        document.getElementById(
            'rm-sources-list'
        );

    if (
        sources &&
        data.sources
    ) {

        sources.innerHTML =
            data.sources
                .map(
                    source =>
                        `
                        <li>
                            ${source.source}
                            -
                            ${source.total}
                        </li>
                        `
                )
                .join('');
    }

    const pages =
        document.getElementById(
            'rm-pages-list'
        );

    if (
        pages &&
        data.top_pages
    ) {

        pages.innerHTML =
            data.top_pages
                .map(
                    page =>
                        `
                        <li>
                            <span>
                                ${page.label}
                            </span>

                            <strong>
                                ${page.total}
                            </strong>
                        </li>
                        `
                )
                .join('');
    }

    const resources =
        document.getElementById(
            'rm-resources-list'
        );

    if (
        resources &&
        data.top_resources
    ) {

        resources.innerHTML =
            data.top_resources
                .map(
                    resource =>
                        `
                        <li>
                            <span>
                                ${resource.recurso}
                            </span>

                            <strong>
                                ${resource.total}
                            </strong>
                        </li>
                        `
                )
                .join('');
    }

    if (
        visitsChart &&
        data.visits
    ) {

        visitsChart.data.labels =
            data.visits.map(
                item => item.dia
            );

        visitsChart.data.datasets[0].data =
            data.visits.map(
                item =>
                    Number(
                        item.total
                    )
            );

        visitsChart.update();
    }
    console.log(data);
}