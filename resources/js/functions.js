export const formatMoney= (value) => {
    if (value === null) {
        return null;
    }

    return Intl.NumberFormat("en-us",{
        style:"currency",
        currency:"USD"
    }).format(value);

};