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
        )
    const data =
        await response.json();
    
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