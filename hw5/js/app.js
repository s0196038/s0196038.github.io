window.addEventListener('DOMContentLoaded', function (event) {

    const bd = [
        {
            id: 0,
            name: "Пончик",
            cost: 50
        },
        {
            id: 1,
            name: "Пончик с шоколадом",
            cost: 80
        },
        {
            id: 2,
            name: "Пончик с клубникой",
            cost: 80
        },
        {
            id: 3,
            name: "Пончик со смородиной",
            cost: 80
        },
        {
            id: 4,
            name: "Пончик с сахарной пудрой",
            cost: 70
        },
    ];

    document.querySelector("#button1").onclick = (event) => {

        event.preventDefault();

        const number_of_products = document.getElementsByName("numberfield")[0].value;
        const product_select = document.getElementsByName("selectfield")[0].value;

        if (isValidValue(number_of_products)) {
            let product_cost = bd[product_select].cost;

            document.getElementById("result").innerHTML = "Итоговая стоимость составляет " + product_cost * Number(number_of_products);
        }
        else {
            alert("Введите корректное количество товаров");
        }
    };

});

function isValidValue(val) {
    return val.match(/^\d+$/) !== null;
}