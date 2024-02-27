# Hydrometer Server 2

A simple server to capture incoming data from fermentation tracking hydrometers.

Supported:

- ISpindle
- Tilt

## Todo's

- ~~Add console command to run tcp server~~
- ~~Implement dispatch of `AddDataCommand` in console~~
- ~~Implement projections for `HydrometerDataReceivedEvent` (just save to a json file named by hydrometer-id)~~
- ~~Implement displaying data with `c3.js`~~
- Document event flows
- ~~Give better instructions on `new hydrometer` page.~~
- Add and implement `DataArchivedEvent` (just copy the data to a newly generated id)

## Example data

Push to tcp:

`telnet 127.0.0.1 10860`

```bash
telnet 127.0.0.1 10860 <<JSON
{
    "name": "eSpindel",
    "ID": "123456",
    "angle": 71.10,
    "temperature": 18.25,
    "battery": 5.54,
    "gravity": 12.89,
    "token": "01HQ1E4H91MNFPPH905TMN8BDJ"
}
JSON
```

```bash
telnet 127.0.0.1 10860 <<JSON
{
    "name": "eSpindel",
    "ID": "123456",
    "angle": 65.0,
    "temperature": 18.00,
    "battery": 5.54,
    "gravity": 10.98,
    "token": "01HQ1E4H91MNFPPH905TMN8BDJ"
}
JSON
```

```bash
telnet 127.0.0.1 10860 <<JSON
{
    "name": "eSpindel",
    "ID": "123456",
    "angle": 54.32,
    "temperature": 20.00,
    "battery": 5.54,
    "gravity": 7.65,
    "token": "01HQ1E4H91MNFPPH905TMN8BDJ"
}
JSON
```

```bash
telnet 127.0.0.1 10860 <<JSON
{
    "name": "eSpindel",
    "ID": "123456",
    "angle": 35.67,
    "temperature": 10.00,
    "battery": 5.54,
    "gravity": 4.32,
    "token": "01HQ1E4H91MNFPPH905TMN8BDJ"
}
JSON
```
