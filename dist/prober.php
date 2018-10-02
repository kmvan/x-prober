<?php
namespace InnStudio\Prober\PreDefine;
\define('TIMER', \microtime(true));
\define('DEBUG', false);
\define('LANG', 'eyJ6aF9DTiI6eyJQbGVhc2Ugd2FpdCAlZCBzZWNvbmRzIjoiXHU4YmY3XHU3YjQ5XHU1Zjg1ICVkIFx1NzlkMiIsIkRhdGFiYXNlIjoiXHU2NTcwXHU2MzZlXHU1ZTkzIiwiREIiOiJcdTY1NzBcdTYzNmVcdTVlOTMiLCJTUUxpdGUzIjoiIiwiU1FMaXRlIjoiIiwiTXlTUUxpIGNsaWVudCI6IiIsIk1vbmdvIjoiIiwiTW9uZ29EQiI6IiIsIlBvc3RncmVTUUwiOiIiLCJQYXJhZG94IjoiUGFyIiwiTWljcm9zb2Z0IFNRTCBTZXJ2ZXIgRHJpdmVyIGZvciBQSFAiOiIiLCJNUyBTUUwiOiIiLCJGaWxlIFBybyI6IiIsIk1heERCIGNsaWVudCI6IiIsIk1heERCIHNlcnZlciI6IiIsIkdlbmVyYXRvciAlcyI6Ilx1OGJlNVx1OTg3NVx1OTc2Mlx1NzUzMSAlcyBcdTc1MWZcdTYyMTAiLCJBdXRob3IgJXMiOiJcdTRmNWNcdTgwMDVcdTRlM2EgJXMiLCJVbmF2YWlsYWJsZSI6Ilx1NGUwZFx1NTNlZlx1NzUyOCIsIiVzIGNhY2hlIjoiJXMgXHU3ZjEzXHU1YjU4IiwiJTEkZGQgJTIkZGggJTMkZG0gJTQkZHMiOiIlMSRkIFx1NTkyOSAlMiRkIFx1NjVmNiAlMyRkIFx1NTIwNiAlNCRkIFx1NzlkMiIsIk5vdCBzdXBwb3J0IG9uIFdpbmRvd3MiOiJXaW5kb3dzIFx1N2NmYlx1N2VkZlx1NWMxYVx1NjcyYVx1NjUyZlx1NjMwMVx1OGJlNVx1NTI5Zlx1ODBmZCIsIiVkIG1pbjoiOiIlZCBcdTUyMDZcdWZmMWEiLCJNeSBpbmZvcm1hdGlvbiI6Ilx1NjIxMVx1NzY4NFx1NGZlMVx1NjA2ZiIsIk1pbmUiOiJcdTYyMTFcdTc2ODQiLCJOZXR3b3JrIHN0YXRzIjoiXHU2ZDQxXHU5MWNmXHU3ZWRmXHU4YmExIiwiTmV0IjoiXHU3ZjUxXHU3ZWRjIiwiUEhQIGV4dGVuc2lvbnMiOiJQSFAgXHU2MjY5XHU1YzU1IiwiRXh0IjoiXHU2MjY5XHU1YzU1IiwiJXMgZXh0ZW5zaW9uIjoiJXMgXHU2MjY5XHU1YzU1IiwiJXMgZW5hYmxlZCI6IiVzIFx1NWRmMlx1NTQyZlx1NzUyOCIsIlplbmQgT3B0aW1pemVyIjoiWmVuZCBcdTRmMThcdTUzMTZcdTU2NjgiLCJMb2FkZWQgZXh0ZW5zaW9ucyI6Ilx1NWRmMlx1NTJhMFx1OGY3ZFx1NzY4NFx1NjI2OVx1NWM1NSIsIlBIUCBpbmZvcm1hdGlvbiI6IlBIUCBcdTRmZTFcdTYwNmYiLCJQSFAiOiJQSFAiLCJQSFAgaW5mbyBkZXRhaWwiOiJQSFAgXHU4YmU2XHU3ZWM2XHU0ZmUxXHU2MDZmIiwiVmVyc2lvbiI6Ilx1NzI0OFx1NjcyYyIsIlNBUEkgaW50ZXJmYWNlIjoiU0FQSSBcdTYzYTVcdTUzZTMiLCJFcnJvciByZXBvcnRpbmciOiJcdTk1MTlcdThiZWZcdTYyYTVcdTU0NGEiLCJNYXggbWVtb3J5IGxpbWl0IjoiXHU4ZmQwXHU4ODRjXHU1MTg1XHU1YjU4XHU5NjUwXHU1MjM2IiwiTWF4IFBPU1Qgc2l6ZSI6IlBPU1QgXHU2M2QwXHU0ZWE0XHU5NjUwXHU1MjM2IiwiTWF4IHVwbG9hZCBzaXplIjoiXHU0ZTBhXHU0ZjIwXHU2NTg3XHU0ZWY2XHU5NjUwXHU1MjM2IiwiTWF4IGlucHV0IHZhcmlhYmxlcyI6Ilx1NjNkMFx1NGVhNFx1ODg2OFx1NTM1NVx1OTY1MFx1NTIzNiIsIk1heCBleGVjdXRpb24gdGltZSI6Ilx1OGZkMFx1ODg0Y1x1OGQ4NVx1NjVmNlx1NzlkMlx1NjU3MCIsIlRpbWVvdXQgZm9yIHNvY2tldCI6IlNvY2tldCBcdThkODVcdTY1ZjZcdTc5ZDJcdTY1NzAiLCJEaXNwbGF5IGVycm9ycyI6Ilx1NjYzZVx1NzkzYVx1OTUxOVx1OGJlZiIsIlRyZWF0bWVudCBVUkxzIGZpbGUiOiJcdTY1ODdcdTRlZjZcdThmZGNcdTdhZWZcdTYyNTNcdTVmMDAiLCJTTVRQIHN1cHBvcnQiOiJTTVRQIFx1NjUyZlx1NjMwMSIsIkRpc2FibGVkIGZ1bmN0aW9ucyI6Ilx1NWRmMlx1Nzk4MVx1NzUyOFx1NzY4NFx1NTFmZFx1NjU3MCIsIlNlcnZlciBCZW5jaG1hcmsiOiJcdTY3MGRcdTUyYTFcdTU2NjhcdThkZDFcdTUyMDYiLCJCZW5jaG1hcmsiOiJcdThkZDFcdTUyMDYiLCJcdWQ4M2RcdWRjYTEgSGlnaGVyIGlzIGJldHRlci4gTm90ZTogdGhlIGJlbmNobWFyayBtYXJrcyBhcmUgbm90IHRoZSBvbmx5IGNyaXRlcmlvbiBmb3IgZXZhbHVhdGluZyB0aGUgcXVhbGl0eSBvZiBhIGhvc3RcL3NlcnZlci4iOiJcdWQ4M2RcdWRjYTEgXHU1MjA2XHU2NTcwXHU4ZDhhXHU5YWQ4XHU4ZDhhXHU1OTdkXHUzMDAyXHU2Y2U4XHU2MTBmXHVmZjFhXHU4ZGQxXHU1MjA2XHU3ZWQzXHU2NzljXHU0ZTBkXHU2NjJmXHU4YmM0XHU0ZWY3XHU2NzBkXHU1MmExXHU1NjY4XHU0ZjE4XHU1MmEzXHU3Njg0XHU1NTJmXHU0ZTAwXHU2ODA3XHU1MWM2XHUzMDAyIiwiRXJyb3IsIGNsaWNrIHRvIHJldHJ5IjoiXHU5NTE5XHU4YmVmXHVmZjBjXHU3MGI5XHU1MWZiXHU5MWNkXHU4YmQ1IiwiTG9hZGluZy4uLiI6Ilx1NTJhMFx1OGY3ZFx1NGUyZFx1MjAyNlx1MjAyNiIsIkFtYXpvblwvRUMyIjoiXHU0ZTlhXHU5YTZjXHU5MDBhXC9FQzIiLCJWUFNTRVJWRVJcL0tWTSI6IlZQU1NFUlZFUlwvS1ZNIiwiU3BhcnRhbkhvc3RcL0tWTSI6Ilx1NjVhZlx1NWRmNFx1OGZiZVwvS1ZNIiwiQWxpeXVuXC9FQ1MiOiJcdTk2M2ZcdTkxY2NcdTRlOTFcL0VDUyIsIlZ1bHRyIjoiVnVsdHIiLCJSYW1Ob2RlIjoiUmFtTm9kZSIsIkxpbm9kZSI6Ikxpbm9kZSIsIlRlbmNlbnQiOiJcdTgxN2VcdThiYWZcdTRlOTEiLCJBbnlOb2RlXC9IREQiOiJBbnlOb2RlXC9IREQiLCJCYW5kd2Fnb25IT1NUXC9TU0QiOiJcdTY0MmNcdTc0ZTZcdTVkZTVcL1NTRCIsIk15IHNlcnZlciI6Ilx1NjIxMVx1NzY4NFx1NjcwZFx1NTJhMVx1NTY2OCIsIkNsaWNrIHRvIHRlc3QiOiJcdTcwYjlcdTUxZmJcdTZkNGJcdThiZDUiLCJTZXJ2ZXIgaW5mb3JtYXRpb24iOiJcdTY3MGRcdTUyYTFcdTU2NjhcdTRmZTFcdTYwNmYiLCJJbmZvIjoiXHU0ZmUxXHU2MDZmIiwiU2VydmVyIG5hbWUiOiJcdTY3MGRcdTUyYTFcdTU2NjhcdTU0MGQiLCJTZXJ2ZXIgdGltZSI6Ilx1NjcwZFx1NTJhMVx1NTY2OFx1NjVmNlx1OTVmNCIsIlNlcnZlciB1cHRpbWUiOiJcdTYzMDFcdTdlZWRcdThmZDBcdTRmNWNcdTY1ZjZcdTk1ZjQiLCJTZXJ2ZXIgSVAiOiJcdTY3MGRcdTUyYTFcdTU2NjggSVAiLCJTZXJ2ZXIgc29mdHdhcmUiOiJcdTY3MGRcdTUyYTFcdTU2NjhcdThmNmZcdTRlZjYiLCJQSFAgdmVyc2lvbiI6IlBIUCBcdTcyNDhcdTY3MmMiLCJDUFUgbW9kZWwiOiJDUFUgXHU1NzhiXHU1M2Y3IiwiU2VydmVyIE9TIjoiXHU2NzBkXHU1MmExXHU1NjY4XHU3Y2ZiXHU3ZWRmIiwiU2NyaXB0IHBhdGgiOiJcdTgxMWFcdTY3MmNcdThkZWZcdTVmODQiLCJEaXNrIHVzYWdlIjoiXHU3OGMxXHU3NmQ4XHU0ZjdmXHU3NTI4XHU5MWNmIiwiU2VydmVyIHN0YXR1cyI6Ilx1NjcwZFx1NTJhMVx1NTY2OFx1NzJiNlx1NjAwMSIsIlN0YXR1cyI6Ilx1NzJiNlx1NjAwMSIsIlN5c3RlbSBsb2FkIjoiXHU3Y2ZiXHU3ZWRmXHU4ZDFmXHU4ZjdkIiwiQ1BVIHVzYWdlIjoiQ1BVIFx1NGY3Zlx1NzUyOFx1NzM4NyIsIlJlYWwgbWVtb3J5IHVzYWdlIjoiXHU3NzFmXHU1YjllXHU1MTg1XHU1YjU4XHU0ZjdmXHU3NTI4IiwiUmVhbCBzd2FwIHVzYWdlIjoiU1dBUCBcdTRmN2ZcdTc1MjgiLCJGaWxlIGNhbiBub3QgdXBkYXRlLiI6Ilx1NjVlMFx1NmNkNVx1NjZmNFx1NjViMFx1NjU4N1x1NGVmNlx1MzAwMiIsIlVwZGF0ZSBmaWxlIG5vdCBmb3VuZC4iOiJcdTY3MmFcdTYyN2VcdTUyMzBcdTY2ZjRcdTY1YjBcdTY1ODdcdTRlZjZcdTMwMDIiLCJVcGRhdGUgc3VjY2Vzcy4uLiI6Ilx1NjZmNFx1NjViMFx1NjIxMFx1NTI5Zlx1MjAyNlx1MjAyNiIsIlVwZGF0ZSBlcnJvci4iOiJcdTY2ZjRcdTY1YjBcdTUxZmFcdTk1MTlcdTMwMDIifSwiemhfVFciOnsiUGxlYXNlIHdhaXQgJWQgc2Vjb25kcyI6Ilx1OGFjYlx1N2I0OVx1NWY4NSAlZCBcdTc5ZDIiLCJEYXRhYmFzZSI6Ilx1OGNjN1x1NjU5OVx1NWVhYiIsIkRCIjoiXHU4Y2M3XHU2NTk5XHU1ZWFiIiwiU1FMaXRlMyI6IiIsIlNRTGl0ZSI6IiIsIk15U1FMaSBjbGllbnQiOiIiLCJNb25nbyI6IiIsIk1vbmdvREIiOiIiLCJQb3N0Z3JlU1FMIjoiIiwiUGFyYWRveCI6IiIsIk1pY3Jvc29mdCBTUUwgU2VydmVyIERyaXZlciBmb3IgUEhQIjoiIiwiTVMgU1FMIjoiIiwiRmlsZSBQcm8iOiIiLCJNYXhEQiBjbGllbnQiOiIiLCJNYXhEQiBzZXJ2ZXIiOiIiLCJHZW5lcmF0b3IgJXMiOiJcdThhNzJcdTk4MDFcdTk3NjJcdTc1MzEgJXMgXHU3NTFmXHU2MjEwIiwiQXV0aG9yICVzIjoiXHU0ZjVjXHU4MDA1XHU3MGJhICVzIiwiVW5hdmFpbGFibGUiOiJcdTRlMGRcdTUzZWZcdTc1MjgiLCIlcyBjYWNoZSI6IiIsIiUxJGRkICUyJGRoICUzJGRtICU0JGRzIjoiIiwiTm90IHN1cHBvcnQgb24gV2luZG93cyI6IldpbmRvd3MgXHU3Y2ZiXHU3ZDcxXHU1YzFhXHU2NzJhXHU2NTJmXHU2M2Y0XHU4YTcyXHU1MjlmXHU4MGZkXHQiLCIlZCBtaW46IjoiIiwiTXkgaW5mb3JtYXRpb24iOiJcdTYyMTFcdTc2ODRcdThhMGFcdTYwNmYiLCJNaW5lIjoiXHU2MjExXHU3Njg0IiwiTmV0d29yayBzdGF0cyI6Ilx1NmQ0MVx1OTFjZlx1N2Q3MVx1OGEwOCIsIk5ldCI6Ilx1NmQ0MVx1OTFjZiIsIlBIUCBleHRlbnNpb25zIjoiUEhQIFx1NjRmNFx1NWM1NSIsIkV4dCI6Ilx1NjRmNFx1NWM1NSIsIiVzIGV4dGVuc2lvbiI6IiVzIFx1NjRmNFx1NWM1NSIsIiVzIGVuYWJsZWQiOiIlcyBcdTU1NWZcdTc1MjgiLCJaZW5kIE9wdGltaXplciI6IiIsIkxvYWRlZCBleHRlbnNpb25zIjoiXHU4ZjA5XHU1MTY1XHU3Njg0IFBIUCBcdTY0ZjRcdTVjNTUiLCJQSFAgaW5mb3JtYXRpb24iOiJQSFAgXHU4YTBhXHU2MDZmIiwiUEhQIjoiUEhQIiwiUEhQIGluZm8gZGV0YWlsIjoiUEhQIFx1OGE3M1x1N2QzMFx1OGEwYVx1NjA2ZiIsIlZlcnNpb24iOiJcdTcyNDhcdTY3MmMiLCJTQVBJIGludGVyZmFjZSI6IlNBUEkgXHU0ZWNiXHU5NzYyIiwiRXJyb3IgcmVwb3J0aW5nIjoiXHU5MzJmXHU4YWE0XHU1ODMxXHU1NDRhIiwiTWF4IG1lbW9yeSBsaW1pdCI6Ilx1NTdmN1x1ODg0Y1x1OGExOFx1NjFiNlx1OWFkNFx1OTY1MFx1NTIzNiIsIk1heCBQT1NUIHNpemUiOiJQT1NUIFx1NjNkMFx1NGVhNFx1OTY1MFx1NTIzNiIsIk1heCB1cGxvYWQgc2l6ZSI6Ilx1NGUwYVx1NTBiM1x1NmE5NFx1Njg0OFx1OTY1MFx1NTIzNiIsIk1heCBpbnB1dCB2YXJpYWJsZXMiOiJcdTYzZDBcdTRlYTRcdTg4NjhcdTU1YWVcdTk2NTBcdTUyMzYiLCJNYXggZXhlY3V0aW9uIHRpbWUiOiJcdTU3ZjdcdTg4NGNcdThkODVcdTY2NDJcdTc5ZDJcdTY1NzgiLCJUaW1lb3V0IGZvciBzb2NrZXQiOiJTb2NrZXQgXHU4ZDg1XHU2NjQyXHU3OWQyXHU2NTc4IiwiRGlzcGxheSBlcnJvcnMiOiJcdTk4NmZcdTc5M2FcdTkzMmZcdThhYTQiLCJUcmVhdG1lbnQgVVJMcyBmaWxlIjoiXHU2YTk0XHU2ODQ4XHU5MDYwXHU3YWVmXHU2MjUzXHU5NThiIiwiU01UUCBzdXBwb3J0IjoiU01UUCBcdTY1MmZcdTYzZjQiLCJEaXNhYmxlZCBmdW5jdGlvbnMiOiJcdTc5ODFcdTc1MjhcdTc2ODRcdTUxZmRcdTY1NzgiLCJTZXJ2ZXIgQmVuY2htYXJrIjoiXHU0ZjNhXHU2NzBkXHU1NjY4XHU2MDI3XHU4MGZkXHU4ZGQxXHU1MjA2IiwiQmVuY2htYXJrIjoiXHU4ZGQxXHU1MjA2IiwiXHVkODNkXHVkY2ExIEhpZ2hlciBpcyBiZXR0ZXIuIE5vdGU6IHRoZSBiZW5jaG1hcmsgbWFya3MgYXJlIG5vdCB0aGUgb25seSBjcml0ZXJpb24gZm9yIGV2YWx1YXRpbmcgdGhlIHF1YWxpdHkgb2YgYSBob3N0XC9zZXJ2ZXIuIjoiXHVkODNkXHVkY2ExIFx1NTIwNlx1NjU3OFx1OGQ4YVx1OWFkOFx1OGQ4YVx1NTk3ZFx1MzAwMlx1NmNlOFx1ZmYxYVx1OGRkMVx1NTIwNlx1NjU3OFx1NTAzY1x1NGUyNlx1NGUwZFx1NjYyZlx1OGE1NVx1NTBmOVx1NGUwMFx1ODFmYVx1NGUzYlx1NmE1Zlx1NjIxNlx1NGYzYVx1NjcwZFx1NTY2OFx1NzY4NFx1NTUyZlx1NGUwMFx1NmU5Nlx1NTI0N1x1MzAwMiIsIkVycm9yLCBjbGljayB0byByZXRyeSI6Ilx1OTMyZlx1OGFhNFx1ZmYwY1x1OWVkZVx1NjRjYVx1OTFjZFx1OGE2NiIsIkxvYWRpbmcuLi4iOiJcdThmMDlcdTUxNjVcdTRlMmRcdTIwMjZcdTIwMjYiLCJBbWF6b25cL0VDMiI6Ilx1NGU5ZVx1OTlhY1x1OTA1Y1wvRUMyIiwiVlBTU0VSVkVSXC9LVk0iOiJWUFNTRVJWRVJcL0tWTSIsIlNwYXJ0YW5Ib3N0XC9LVk0iOiJcdTY1YWZcdTVkZjRcdTkwNTRcL0tWTSIsIkFsaXl1blwvRUNTIjoiXHU5NjNmXHU5MWNjXHU5NmYyXC9FQ1MiLCJWdWx0ciI6IlZ1bHRyIiwiUmFtTm9kZSI6IlJhbU5vZGUiLCJMaW5vZGUiOiJMaW5vZGUiLCJUZW5jZW50IjoiXHU5YTMwXHU4YTBhXHU5NmYyIiwiQW55Tm9kZVwvSEREIjoiQW55Tm9kZVwvSEREIiwiQmFuZHdhZ29uSE9TVFwvU1NEIjoiIiwiTXkgc2VydmVyIjoiXHU2MjExXHU3Njg0XHU0ZjNhXHU2NzBkXHU1NjY4IiwiQ2xpY2sgdG8gdGVzdCI6Ilx1OWVkZVx1NjRjYVx1NmUyY1x1OGE2NiIsIlNlcnZlciBpbmZvcm1hdGlvbiI6Ilx1NGYzYVx1NjcwZFx1NTY2OFx1OGEwYVx1NjA2ZiIsIkluZm8iOiJcdThhMGFcdTYwNmYiLCJTZXJ2ZXIgbmFtZSI6Ilx1NGYzYVx1NjcwZFx1NTY2OFx1NTQwZCIsIlNlcnZlciB0aW1lIjoiXHU2MzAxXHU3ZThjXHU0ZTBhXHU3ZGRhXHU2NjQyXHU5NTkzIiwiU2VydmVyIHVwdGltZSI6Ilx1NjMwMVx1N2U4Y1x1NGUwYVx1N2RkYVx1NjY0Mlx1OTU5MyIsIlNlcnZlciBJUCI6Ilx1NGYzYVx1NjcwZFx1NTY2OCBJUCIsIlNlcnZlciBzb2Z0d2FyZSI6Ilx1NGYzYVx1NjcwZFx1NTY2OFx1OGVkZlx1OWFkNCIsIlBIUCB2ZXJzaW9uIjoiUEhQIFx1NzI0OFx1NjcyYyIsIkNQVSBtb2RlbCI6IkNQVSBcdTU3OGJcdTg2NWYiLCJTZXJ2ZXIgT1MiOiJcdTRmM2FcdTY3MGRcdTU2NjhcdTdjZmJcdTdkNzEiLCJTY3JpcHQgcGF0aCI6Ilx1ODE3M1x1NjcyY1x1OGRlZlx1NWY5MSIsIkRpc2sgdXNhZ2UiOiJcdTc4YzFcdTc4OWZcdTRmN2ZcdTc1MjgiLCJTZXJ2ZXIgc3RhdHVzIjoiXHU0ZjNhXHU2NzBkXHU1NjY4XHU3MmMwXHU2MTRiIiwiU3RhdHVzIjoiXHU3MmMwXHU2MTRiIiwiU3lzdGVtIGxvYWQiOiJcdTdjZmJcdTdkNzFcdThjYTBcdThmMDkiLCJDUFUgdXNhZ2UiOiJDUFUgXHU0ZjdmXHU3NTI4XHU3Mzg3IiwiUmVhbCBtZW1vcnkgdXNhZ2UiOiJcdTc3MWZcdTViZTZcdThhMThcdTYxYjZcdTlhZDRcdTRmN2ZcdTc1MjgiLCJSZWFsIHN3YXAgdXNhZ2UiOiJTV0FQIFx1NGY3Zlx1NzUyOCIsIkZpbGUgY2FuIG5vdCB1cGRhdGUuIjoiXHU2YTk0XHU2ODQ4XHU3MTIxXHU2Y2Q1XHU4OGFiXHU2NmY0XHU2NWIwXHUzMDAyIiwiVXBkYXRlIGZpbGUgbm90IGZvdW5kLiI6Ilx1NjcyYVx1NzY3Y1x1NzNmZVx1NjZmNFx1NjViMFx1NmE5NFx1Njg0OFx1MzAwMiIsIlVwZGF0ZSBzdWNjZXNzLi4uIjoiXHU2NmY0XHU2NWIwXHU2MjEwXHU1MjlmXHUyMDI2XHUyMDI2IiwiVXBkYXRlIGVycm9yLiI6Ilx1NjZmNFx1NjViMFx1NTFmYVx1OTMyZlx1MzAwMiJ9fQ==');
namespace InnStudio\Prober\Nav; use InnStudio\Prober\Events\Api as Events; class Nav { private $ID = 'nav'; public function __construct() { Events::on('script', array($this, 'filterScript')); Events::on('style', array($this, 'filterStyle')); } public function filterStyle() { echo <<<HTML
<style>
.nav {
position: fixed;
bottom: 0;
background: #333;
padding: 0 1rem;
left: 0;
right: 0;
text-align: center;
z-index: 10;
}
.nav a{
display: inline-block;
color: #eee;
padding: .3rem .5rem;
border-left: 1px solid rgba(255,255,255,.05);
}
.nav a:first-child{
border: none;
}
.nav a:hover,
.nav a:focus,
.nav a:active{
background: #f8f8f8;
color: #333;
text-decoration: none;
}
.nav .long-title{
display: none;
}
.nav .tiny-title{
display: block;
}
@media (min-width: 579px) {
.nav .tiny-title{
display: none;
}
.nav .long-title{
display: block;
}
.nav a{
padding: .3rem 1rem;
}
}
</style>
HTML;
} public function filterScript() { echo <<<HTML
<script>
(function(){
var fieldsets = document.querySelectorAll('fieldset');
if (! fieldsets.length) {
return;
}
var nav = document.createElement('div');
nav.className = 'nav';
for(var i = 0; i < fieldsets.length; i++) {
var fieldset = fieldsets[i];
var a = document.createElement('a');
a.href = '#' + encodeURIComponent(fieldset.id);
a.innerHTML = fieldset.querySelector('legend').innerHTML;
nav.appendChild(a);
}
document.body.appendChild(nav);
})()
</script>
HTML;
} }
namespace InnStudio\Prober\PhpExtensionInfo; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class PhpExtensionInfo { private $ID = 'phpExtensionInfo'; public function __construct() { Events::patch('mods', array($this, 'filter'), 400); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18nApi::_('PHP extensions'), 'tinyTitle' => I18nApi::_('Ext'), 'display' => array($this, 'display'), ); return $mods; } public function display() { echo <<<HTML
<div class="row">
{$this->getContent()}
</div>
HTML;
} private function getContent() { $items = array( array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Memcache'), 'content' => Helper::getIni(0, \extension_loaded('memcache') && \class_exists('\\Memcache')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Memcached'), 'content' => Helper::getIni(0, \extension_loaded('memcached') && \class_exists('\\Memcached')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Redis'), 'content' => Helper::getIni(0, \extension_loaded('redis') && \class_exists('\\Redis')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Opcache'), 'content' => Helper::getIni(0, \function_exists('\\opcache_get_configuration')), ), array( 'label' => \sprintf(I18nApi::_('%s enabled'), 'Opcache'), 'content' => Helper::getIni(0, $this->isOpcEnabled()), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Swoole'), 'content' => Helper::getIni(0, \extension_loaded('Swoole') && \function_exists('\\swoole_version')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Imagick'), 'content' => Helper::getIni(0, \extension_loaded('Imagick') && \class_exists('\\Imagick')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Exif'), 'content' => Helper::getIni(0, \extension_loaded('Exif') && \function_exists('\\exif_imagetype')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Sockets'), 'content' => Helper::getIni(0, \extension_loaded('Sockets') && \function_exists('\\socket_accept')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'MySQLi'), 'content' => Helper::getIni(0, \extension_loaded('MySQLi') && \class_exists('\\mysqli')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Zip'), 'content' => Helper::getIni(0, \extension_loaded('Zip') && \class_exists('\\ZipArchive')), ), array( 'label' => \sprintf(I18nApi::_('%s extension'), 'Multibyte String'), 'content' => Helper::getIni(0, \extension_loaded('mbstring') && \function_exists('\\mb_substr')), ), array( 'label' => I18nApi::_('Zend Optimizer'), 'content' => Helper::getIni(0, \function_exists('zend_optimizer_version')), ), array( 'col' => '1-1', 'label' => I18nApi::_('Loaded extensions'), 'title' => 'loaded_extensions', 'content' => \implode(', ', $this->getLoadedExtensions(true)) ?: '-', ), ); $itemsOrder = array(); foreach ($items as $item) { $itemsOrder[] = $item['label']; } \array_multisort($items, $itemsOrder); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; $content .= <<<HTML
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$title} {$id}>{$item['content']}</div>
</div>
</div>
HTML;
} return $content; } private function getLoadedExtensions($sorted = false) { $exts = \get_loaded_extensions(); if ($sorted) { \sort($exts); } return $exts; } private function isOpcEnabled() { $isOpcEnabled = \function_exists('\\opcache_get_configuration'); if ($isOpcEnabled) { $isOpcEnabled = \opcache_get_configuration(); $isOpcEnabled = isset($isOpcEnabled['directives']['opcache.enable']) && true === $isOpcEnabled['directives']['opcache.enable']; } return $isOpcEnabled; } }
namespace InnStudio\Prober\Helper; use InnStudio\Prober\I18n\I18nApi; class Api { public static function dieJson($data) { \header('Content-Type: application/json'); die(\json_encode($data)); } public static function isAction($action) { return \filter_input(\INPUT_GET, 'action', \FILTER_SANITIZE_STRING) === $action; } public static function getWinCpuUsage() { $cpus = array(); if (\class_exists('\\COM')) { $wmi = new \COM('Winmgmts://'); $server = $wmi->execquery('SELECT LoadPercentage FROM Win32_Processor'); $cpus = array(); foreach ($server as $cpu) { $total += (int) $cpu->loadpercentage; } $total = (int) $total / \count($server); $cpus['idle'] = 100 - $total; $cpus['user'] = $total; } else { \exec('wmic cpu get LoadPercentage', $p); if (isset($p[1])) { $percent = (int) $p[1]; $cpus['idle'] = 100 - $percent; $cpus['user'] = $percent; } } return $cpus; } public static function getNetworkStats() { $filePath = '/proc/net/dev'; if ( ! \is_readable($filePath)) { return I18nApi::_('Unavailable'); } static $eths = null; if (null !== $eths) { return $eths; } $lines = \file($filePath); unset($lines[0], $lines[1]); $eths = array(); foreach ($lines as $line) { $line = \preg_replace('/\s+/', ' ', \trim($line)); $lineArr = \explode(':', $line); $numberArr = \explode(' ', \trim($lineArr[1])); $eths[$lineArr[0]] = array( 'rx' => (int) $numberArr[0], 'tx' => (int) $numberArr[8], ); } return $eths; } public static function getBtn($tx, $url) { return <<<HTML
<a href="{$url}" target="_blank" class="btn">{$tx}</a>
HTML;
} public static function getDiskTotalSpace($human = false) { static $space = null; if (null === $space) { $dir = self::isWin() ? 'C:' : '/'; if ( ! \is_readable($dir)) { $space = 0; return 0; } $space = \disk_total_space($dir); } if ( ! $space) { return 0; } if (true === $human) { return self::formatBytes($space); } return $space; } public static function getDiskFreeSpace($human = false) { static $space = null; if (null === $space) { $dir = self::isWin() ? 'C:' : '/'; if ( ! \is_readable($dir)) { $space = 0; return 0; } $space = \disk_free_space($dir); } if ( ! $space) { return 0; } if (true === $human) { return self::formatBytes($space); } return $space; } public static function getCpuModel() { $filePath = '/proc/cpuinfo'; if ( ! \is_readable($filePath)) { return I18nApi::_('Unavailable'); } $content = \file_get_contents($filePath); $cores = \substr_count($content, 'cache size'); $lines = \explode("\n", $content); $modelName = \explode(':', $lines[4]); $modelName = \trim($modelName[1]); $cacheSize = \explode(':', $lines[8]); $cacheSize = \trim($cacheSize[1]); return "{$cores} x {$modelName} / " . \sprintf(I18nApi::_('%s cache'), $cacheSize); } public static function getServerTime() { return \date('Y-m-d H:i:s'); } public static function getServerUpTime() { $filePath = '/proc/uptime'; if ( ! \is_readable($filePath)) { return I18nApi::_('Unavailable'); } $str = \file_get_contents($filePath); $num = (float) $str; $secs = \fmod($num, 60); $num = (int) ($num / 60); $mins = $num % 60; $num = (int) ($num / 60); $hours = $num % 24; $num = (int) ($num / 24); $days = $num; return \sprintf( I18nApi::_('%1$dd %2$dh %3$dm %4$ds'), $days, $hours, $mins, $secs ); } public static function getErrNameByCode($code) { $levels = array( \E_ALL => 'E_ALL', \E_USER_DEPRECATED => 'E_USER_DEPRECATED', \E_DEPRECATED => 'E_DEPRECATED', \E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR', \E_STRICT => 'E_STRICT', \E_USER_NOTICE => 'E_USER_NOTICE', \E_USER_WARNING => 'E_USER_WARNING', \E_USER_ERROR => 'E_USER_ERROR', \E_COMPILE_WARNING => 'E_COMPILE_WARNING', \E_COMPILE_ERROR => 'E_COMPILE_ERROR', \E_CORE_WARNING => 'E_CORE_WARNING', \E_CORE_ERROR => 'E_CORE_ERROR', \E_NOTICE => 'E_NOTICE', \E_PARSE => 'E_PARSE', \E_WARNING => 'E_WARNING', \E_ERROR => 'E_ERROR', ); $result = ''; foreach ($levels as $number => $name) { if (($code & $number) == $number) { $result .= ('' != $result ? ', ' : '') . $name; } } return $result; } public static function getIni($id, $forceSet = null) { if (true === $forceSet) { $ini = 1; } elseif (false === $forceSet) { $ini = 0; } else { $ini = \ini_get($id); } if ( ! \is_numeric($ini) && '' !== (string) $ini) { return $ini; } if (1 === (int) $ini) { return <<<HTML
<span class="ini-ok">&check;</span>
HTML;
} if (0 === (int) $ini) { return <<<HTML
<span class="ini-error">&times;</span>
HTML;
} return $ini; } public static function isWin() { return \PHP_OS === 'WINNT'; } public static function htmlMinify($buffer) { \preg_match_all('#\<textarea.*\>.*\<\/textarea\>#Uis', $buffer, $foundTxt); \preg_match_all('#\<pre.*\>.*\<\/pre\>#Uis', $buffer, $foundPre); $textareas = array(); foreach (\array_keys($foundTxt[0]) as $item) { $textareas[] = '<textarea>' . $item . '</textarea>'; } $pres = array(); foreach (\array_keys($foundPre[0]) as $item) { $pres[] = '<pre>' . $item . '</pre>'; } $buffer = \str_replace($foundTxt[0], $textareas, $buffer); $buffer = \str_replace($foundPre[0], $pres, $buffer); $search = array( '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', ); $replace = array( '>', '<', '\\1', ); $buffer = \preg_replace($search, $replace, $buffer); $textareas = array(); foreach (\array_keys($foundTxt[0]) as $item) { $textareas[] = '<textarea>' . $item . '</textarea>'; } $pres = array(); foreach (\array_keys($foundPre[0]) as $item) { $pres[] = '<pre>' . $item . '</pre>'; } $buffer = \str_replace($textareas, $foundTxt[0], $buffer); $buffer = \str_replace($pres, $foundPre[0], $buffer); return $buffer; } public static function getClientIp() { $keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'); foreach ($keys as $key) { if ( ! isset($_SERVER[$key])) { continue; } $ip = \array_filter(\explode(',', $_SERVER[$key])); $ip = \filter_var(\end($ip), \FILTER_VALIDATE_IP); if ($ip) { return $ip; } } return ''; } public static function getCpuUsage() { static $cpu = null; if (null !== $cpu) { return $cpu; } if (self::isWin()) { $cpu = self::getWinCpuUsage(); return $cpu; } $filePath = ('/proc/stat'); if ( ! \is_readable($filePath)) { $cpu = array(); return $cpu; } $stat1 = \file($filePath); \sleep(1); $stat2 = \file($filePath); $info1 = \explode(' ', \preg_replace('!cpu +!', '', $stat1[0])); $info2 = \explode(' ', \preg_replace('!cpu +!', '', $stat2[0])); $dif = array(); $dif['user'] = $info2[0] - $info1[0]; $dif['nice'] = $info2[1] - $info1[1]; $dif['sys'] = $info2[2] - $info1[2]; $dif['idle'] = $info2[3] - $info1[3]; $total = \array_sum($dif); $cpu = array(); foreach ($dif as $x => $y) { $cpu[$x] = \round($y / $total * 100, 1); } return $cpu; } public static function getHumanCpuUsageDetail() { $cpu = self::getCpuUsage(); if ( ! $cpu) { return ''; } $html = ''; foreach ($cpu as $k => $v) { $html .= <<<HTML
<span class="small-group"><span class="item-name">{$k}</span>
<span class="item-value">{$v}</span></span>
HTML;
} return $html; } public static function getHumanCpuUsage() { $cpu = self::getCpuUsage(); return $cpu ?: array(); } public static function getSysLoadAvg() { if (self::isWin()) { return I18nApi::_('Not support on Windows'); } $avg = \sys_getloadavg(); $langMin = function ($n) { return \sprintf(I18nApi::_('%d minute(s)'), $n); }; $avg = \array_map(function ($load) { $load = \sprintf('%.2f', $load); return <<<HTML
<span class="small-group">
{$load}
</span>
HTML;
}, $avg); return \implode('', $avg); } public static function getMemoryUsage($key) { $key = \ucfirst($key); if (self::isWin()) { return 0; } static $memInfo = null; if (null === $memInfo) { $memInfoFile = '/proc/meminfo'; if ( ! \is_readable($memInfoFile)) { $memInfo = 0; return 0; } $memInfo = \file_get_contents($memInfoFile); $memInfo = \str_replace(array( ' kB', '  ', ), '', $memInfo); $lines = array(); foreach (\explode("\n", $memInfo) as $line) { if ( ! $line) { continue; } $line = \explode(':', $line); $lines[$line[0]] = (int) $line[1]; } $memInfo = $lines; } switch ($key) { case 'MemRealUsage': $memAvailable = 0; if (isset($memInfo['MemAvailable'])) { $memAvailable = $memInfo['MemAvailable']; } elseif (isset($memInfo['MemFree'])) { $memAvailable = $memInfo['MemFree']; } return $memInfo['MemTotal'] - $memAvailable; case 'SwapRealUsage': if ( ! isset($memInfo['SwapTotal']) || ! isset($memInfo['SwapFree']) || ! isset($memInfo['SwapCached'])) { return 0; } return $memInfo['SwapTotal'] - $memInfo['SwapFree'] - $memInfo['SwapCached']; } return isset($memInfo[$key]) ? (int) $memInfo[$key] : 0; } public static function formatBytes($bytes, $precision = 2) { if ( ! $bytes) { return 0; } $base = \log($bytes, 1024); $suffixes = array('', ' K', ' M', ' G', ' T'); return \round(\pow(1024, $base - \floor($base)), $precision) . $suffixes[\floor($base)]; } public static function getHumamMemUsage($key) { return self::formatBytes(self::getMemoryUsage($key) * 1024); } public static function strcut($str, $len = 20) { if (\strlen($str) > $len) { return \mb_strcut($str, 0, $len) . '...'; } return $str; } }
namespace InnStudio\Prober\Benchmark; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class Benchmark { private $EXPIRED = 60; public function __construct() { Events::on('init', array($this, 'filter')); } public function filter() { if ( ! Helper::isAction('benchmark')) { return; } $this->display(); } private function getTmpRecorderPath() { return \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'xproberBenchmarkTimer'; } private function saveTmpRecorder() { return (bool) \file_put_contents($this->getTmpRecorderPath(), \json_encode(array( 'expired' => (int) $_SERVER['REQUEST_TIME'] + $this->EXPIRED, ))); } private function getRemainingSeconds() { $path = $this->getTmpRecorderPath(); if ( ! \is_readable($path)) { return 0; } $data = (string) \file_get_contents($this->getTmpRecorderPath()); if ( ! $data) { return 0; } $data = \json_decode($data, true); if ( ! $data) { return 0; } $expired = isset($data['expired']) ? (int) $data['expired'] : 0; if ( ! $expired) { return 0; } return $expired > (int) $_SERVER['REQUEST_TIME'] ? $expired - (int) $_SERVER['REQUEST_TIME'] : 0; } private function getPointsByTime($time) { return \pow(10, 3) - (int) ($time * \pow(10, 3)); } private function getHashPoints() { $data = 'inn-studio.com'; $hash = array('md5', 'sha512', 'sha256', 'crc32'); $count = \pow(10, 5); $start = \microtime(true); for ($i = 0; $i < $count; ++$i) { foreach ($hash as $v) { \hash($v, $data); } } return $this->getPointsByTime(\microtime(true) - $start); } private function getIntLoopPoints() { $j = 0; $count = \pow(10, 7); $start = \microtime(true); for ($i = 0; $i < $count; ++$i) { ++$j; } return $this->getPointsByTime(\microtime(true) - $start); } private function getFloatLoopPoints() { $j = 1 / 3; $count = \pow(10, 7); $start = \microtime(true); for ($i = 0; $i < $count; ++$i) { ++$j; } return $this->getPointsByTime(\microtime(true) - $start); } private function getIoLoopPoints() { $tmpDir = \sys_get_temp_dir(); if ( ! \is_writable($tmpDir)) { return 0; } $count = \pow(10, 4); $start = \microtime(true); for ($i = 0; $i < $count; ++$i) { $filePath = "{$tmpDir}/innStudioIoBenchmark:{$i}"; \file_put_contents($filePath, $filePath); \unlink($filePath); } return $this->getPointsByTime(\microtime(true) - $start); } private function getPoints() { return array( 'hash' => $this->getHashPoints(), 'intLoop' => $this->getIntLoopPoints(), 'floatLoop' => $this->getFloatLoopPoints(), 'ioLoop' => $this->getIoLoopPoints(), ); } private function display() { $remainingSeconds = $this->getRemainingSeconds(); if ($remainingSeconds) { Helper::dieJson(array( 'code' => -1, 'msg' => \sprintf(I18nApi::_('Please wait %d seconds'), $remainingSeconds), )); } $this->saveTmpRecorder(); \set_time_limit(0); Helper::dieJson(array( 'code' => 0, 'data' => array( 'points' => $this->getPoints(), ), )); } }
namespace InnStudio\Prober\PhpInfoDetail; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; class PhpInfoDetail { public function __construct() { Events::on('init', array($this, 'filter')); } public function filter() { if (Helper::isAction('phpInfo')) { \phpinfo(); die; } } }
namespace InnStudio\Prober\Entry; use InnStudio\Prober\Config\Api as Config; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class Entry { public function __construct() { Events::emit('init'); if (DEBUG === true) { $this->display(); } else { \ob_start(); $this->display(); $content = \ob_get_contents(); \ob_end_clean(); echo Helper::htmlMinify($content); } } private function displayContent() { $mods = Events::apply('mods', array()); if ( ! $mods) { return; } foreach ($mods as $id => $mod) { ?>
<fieldset id="<?php echo $id; ?>">
<legend >
<span class="long-title"><?php echo $mod['title']; ?></span>
<span class="tiny-title"><?php echo $mod['tinyTitle']; ?></span>
</legend>
<?php \call_user_func($mod['display']); ?>
</fieldset>
<?php
} } private function display() { ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title><?php echo I18nApi::_(Config::$APP_NAME); ?> v<?php echo Config::$APP_VERSION; ?></title>
<?php Events::emit('style'); ?>
</head>
<body>
<div class="poi-container">
<h1><a href="<?php echo I18nApi::_(Config::$APP_URL); ?>" target="_blank"><?php echo I18nApi::_(Config::$APP_NAME); ?> v<?php echo Config::$APP_VERSION; ?></a></h1>
<?php $this->displayContent(); ?>
</div>
<?php Events::emit('footer'); ?>
<?php Events::emit('script'); ?>
</body>
</html>
<?php
} }
namespace InnStudio\Prober\Database; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class Database { private $ID = 'database'; public function __construct() { Events::patch('mods', array($this, 'filter'), 500); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18nApi::_('Database'), 'tinyTitle' => I18nApi::_('DB'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} private function getContent() { $sqlite3Version = \class_exists('\\SQLite3') ? \SQLite3::version() : false; $sqlite3Version = $sqlite3Version ? Helper::getIni(0, true) . ' ' . $sqlite3Version['versionString'] : Helper::getIni(0, false); $items = array( array( 'label' => I18nApi::_('SQLite3'), 'content' => $sqlite3Version, ), array( 'title' => 'sqlite_libversion', 'label' => I18nApi::_('SQLite'), 'content' => \function_exists('\\sqlite_libversion') ? Helper::getIni(0, true) . ' ' . \sqlite_libversion() : Helper::getIni(0, false), ), array( 'title' => 'mysqli_get_client_version', 'label' => I18nApi::_('MySQLi client'), 'content' => \function_exists('\\mysqli_get_client_version') ? Helper::getIni(0, true) . ' ' . \mysqli_get_client_version() : Helper::getIni(0, false), ), array( 'label' => I18nApi::_('Mongo'), 'content' => \class_exists('\\Mongo') ? \MongoClient::VERSION : Helper::getIni(0, false), ), array( 'label' => I18nApi::_('MongoDB'), 'content' => \class_exists('\\MongoDB') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'label' => I18nApi::_('PostgreSQL'), 'content' => \function_exists('\\pg_connect') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'label' => I18nApi::_('Paradox'), 'content' => \function_exists('\\px_new') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'title' => I18nApi::_('Microsoft SQL Server Driver for PHP'), 'label' => I18nApi::_('MS SQL'), 'content' => \function_exists('\\sqlsrv_server_info') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'label' => I18nApi::_('File Pro'), 'content' => \function_exists('\\filepro') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'label' => I18nApi::_('MaxDB client'), 'content' => \function_exists('\\maxdb_get_client_version') ? \maxdb_get_client_version() : Helper::getIni(0, false), ), array( 'label' => I18nApi::_('MaxDB server'), 'content' => \function_exists('\\maxdb_get_server_version') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), ); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; echo <<<HTML
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$id} {$title}>{$item['content']}</div>
</div>
</div>
HTML;
} } }
namespace InnStudio\Prober\PhpInfo; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class PhpInfo { private $ID = 'phpInfo'; public function __construct() { Events::patch('mods', array($this, 'filter'), 300); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18nApi::_('PHP information'), 'tinyTitle' => I18nApi::_('PHP'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} private function getContent() { $errLevels = Helper::getErrNameByCode(\ini_get('error_reporting')); $items = array( array( 'label' => $this->_('PHP info detail'), 'content' => Helper::getBtn("👆 {$this->_('Click to check')}", '?action=phpInfo'), ), array( 'label' => $this->_('Version'), 'content' => \PHP_VERSION, ), array( 'label' => $this->_('SAPI interface'), 'content' => \PHP_SAPI, ), array( 'label' => $this->_('Error reporting'), 'title' => "error_reporting: {$errLevels}", 'content' => Helper::strcut($errLevels), ), array( 'label' => $this->_('Max memory limit'), 'title' => 'memory_limit', 'content' => \ini_get('memory_limit'), ), array( 'label' => $this->_('Max POST size'), 'title' => 'post_max_size', 'content' => \ini_get('post_max_size'), ), array( 'label' => $this->_('Max upload size'), 'title' => 'upload_max_filesize', 'content' => \ini_get('upload_max_filesize'), ), array( 'label' => $this->_('Max input variables'), 'title' => 'max_input_vars', 'content' => \ini_get('max_input_vars'), ), array( 'label' => $this->_('Max execution time'), 'title' => 'max_execution_time', 'content' => \ini_get('max_execution_time'), ), array( 'label' => $this->_('Timeout for socket'), 'title' => 'default_socket_timeout', 'content' => \ini_get('default_socket_timeout'), ), array( 'label' => $this->_('Display errors'), 'title' => 'display_errors', 'content' => Helper::getIni('display_errors'), ), array( 'label' => $this->_('Treatment URLs file'), 'title' => 'allow_url_fopen', 'content' => Helper::getIni('allow_url_fopen'), ), array( 'label' => $this->_('SMTP support'), 'title' => 'SMTP', 'content' => Helper::getIni('SMTP') ?: Helper::getIni(0, false), ), array( 'col' => '1-1', 'label' => $this->_('Disabled functions'), 'title' => 'disable_functions', 'content' => \implode(', ', \explode(',', Helper::getIni('disable_functions'))) ?: '-', ), ); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; $content .= <<<HTML
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$title} {$id}>{$item['content']}</div>
</div>
</div>
HTML;
} return $content; } private function _($str) { return I18nApi::_($str); } }
namespace InnStudio\Prober\Script; use InnStudio\Prober\Events\Api as Events; class Script { private $ID = 'script'; public function __construct() { Events::on('script', array($this, 'filter')); } public function filter() { echo <<<HTML
<script>
(function () {
var xhr = new XMLHttpRequest();
xhr.onload = load;
var cache = {};
function addClassName(el,className){
if (el.classList){
el.classList.add(className);
} else {
el.className += ' ' + className;
}
}
function removeClassName(el, className){
if (el.classList){
el.classList.remove(className);
} else {
el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
}
}
function formatBytes(bytes, decimals) {
if (bytes == 0) {
return '0';
}
var k = 1024,
dm = decimals || 2,
sizes = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'],
i = Math.floor(Math.log(bytes) / Math.log(k));
return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
function I(el) {
if (cache[el]) {
return cache[el];
}
cache[el] = document.getElementById(el);
return cache[el];
}
function setColor(progress, percent) {
if (percent >= 80) {
addClassName(progress,'high');
removeClassName(progress,'medium');
removeClassName(progress,'medium-low');
} else if (percent >= 50) {
addClassName(progress,'medium');
removeClassName(progress,'high');
removeClassName(progress,'medium-low');
} else if (percent >= 30) {
addClassName(progress,'medium-low');
removeClassName(progress,'medium');
removeClassName(progress,'high');
} else {
removeClassName(progress,'high');
removeClassName(progress,'medium');
removeClassName(progress,'medium-low');
}
}
function request() {
xhr.open('get', '?action=fetch');
xhr.send();
}
function load() {
if (xhr.readyState !== 4) {
return;
}
if (xhr.status >= 200 && xhr.status < 400) {
var res = JSON.parse(xhr.responseText);
if (res && res.code === 0) {
var data = res.data;
fillCpuUsage(data);
fillSysLoadAvg(data);
fillMemRealUsage(data);
fillSwapRealUsage(data);
fillServerInfo(data);
fillNetworkStats(data);
}
} else {}
setTimeout(function () {
request();
}, 1000);
}
function fillCpuUsage(data) {
var progress = I('cpuUsageProgress');
var value = I('cpuUsageProgressValue');
var percent = 100 - Math.round(data.cpuUsage.idle);
var title = [];
for (var i in data.cpuUsage) {
title.push(i + ': ' + data.cpuUsage[i]);
}
progress.title = title.join(' / ');
value.style.width = percent + '%';
setColor(progress, percent);
I('cpuUsagePercent').innerHTML = percent + '%';
}
function fillSysLoadAvg(data) {
I('systemLoadAvg').innerHTML = data.sysLoadAvg;
}
function fillMemRealUsage(data) {
var progress = I('memRealUsageProgress');
var value = I('memRealUsageProgressValue');
var percent = data.memRealUsage.percent;
value.style.width = percent + '%';
setColor(progress, percent);
I('memRealUsagePercent').innerHTML = percent + '%';
I('memRealUsage').innerHTML = data.memRealUsage.number;
}
function fillSwapRealUsage(data) {
var progress = I('swapRealUsageProgress');
var value = I('swapRealUsageProgressValue');
var percent = data.swapRealUsage.percent;
value.style.width = percent + '%';
setColor(progress, percent);
I('swapRealUsagePercent').innerHTML = percent + '%';
I('swapRealUsage').innerHTML = data.swapRealUsage.number
}
function fillServerInfo(data) {
I('serverInfoTime').innerHTML = data.serverInfo.time;
I('serverUpTime').innerHTML = data.serverInfo.upTime;
}
var lastNetworkStats = {};
function fillNetworkStats(data) {
if (typeof data.networkStats !== 'object') {
return;
}
var keys = Object.keys(data.networkStats);
if (keys.length === 0) {
return;
}
keys.map(function (k) {
var item = data.networkStats[k];
['rx', 'tx'].map(function (type) {
var total = data.networkStats[k][type];
var last = lastNetworkStats[k] && lastNetworkStats[k][type] || 0;
I('network-' + k + '-' + type + '-rate').innerHTML = last ? formatBytes((total - last) / 2) : 0;
I('network-' + k + '-' + type + '-total').innerHTML = formatBytes(total);
if (!lastNetworkStats[k]) {
lastNetworkStats[k] = {};
}
lastNetworkStats[k][type] = total;
});
});
}
request();
})();
</script>
HTML;
} }
namespace InnStudio\Prober\Events; class Api { private static $filters = array(); private static $actions = array(); private static $PRIORITY_ID = 'priority'; private static $CALLBACK_ID = 'callback'; public static function on($name, $callback, $priority = 10) { if ( ! isset(self::$actions[$name])) { self::$actions[$name] = array(); } self::$actions[$name][] = array( self::$PRIORITY_ID => $priority, self::$CALLBACK_ID => $callback, ); } public static function emit() { $args = \func_get_args(); $name = $args[0]; unset($args[0]); $actions = isset(self::$actions[$name]) ? self::$actions[$name] : false; if ( ! $actions) { return; } $sortArr = array(); foreach ($actions as $k => $action) { $sortArr[$k] = $action[self::$PRIORITY_ID]; } \array_multisort($sortArr, $actions); foreach ($actions as $action) { \call_user_func_array($action[self::$CALLBACK_ID], $args); } } public static function patch($name, $callback, $priority = 10) { if ( ! isset(self::$filters[$name])) { self::$filters[$name] = array(); } self::$filters[$name][] = array( self::$PRIORITY_ID => $priority, self::$CALLBACK_ID => $callback, ); } public static function apply() { $args = \func_get_args(); $name = $args[0]; $return = $args[1]; unset($args[0],$args[1]); $filters = isset(self::$filters[$name]) ? self::$filters[$name] : false; if ( ! $filters) { return $return; } $sortArr = array(); foreach ($filters as $k => $filter) { $sortArr[$k] = $filter[self::$PRIORITY_ID]; } \array_multisort($sortArr, $filters); foreach ($filters as $filter) { $return = \call_user_func_array($filter[self::$CALLBACK_ID], array($return, $args)); } return $return; } }
namespace InnStudio\Prober\MyInfo; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class MyInfo { private $ID = 'myInfo'; public function __construct() { Events::patch('mods', array($this, 'filter'), 900); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18nApi::_('My information'), 'tinyTitle' => I18nApi::_('Mine'), 'display' => array($this, 'display'), ); return $mods; } public function display() { echo $this->getContent(); } public function getContent() { $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''; $lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : ''; return <<<HTML
<div class="form-group">
<div class="group-label">{$this->_('My IP')}</div>
<div class="group-content">{$this->getClientIp()}</div>
</div>
<div class="form-group">
<div class="group-label">{$this->_('My browser UA')}</div>
<div class="group-content">{$ua}</div>
</div>
<div class="form-group">
<div class="group-label">{$this->_('My browser language')}</div>
<div class="group-content">{$lang}</div>
</div>
HTML;
} private function getClientIp() { return Helper::getClientIp(); } private function _($str) { return I18nApi::_($str); } }
namespace InnStudio\Prober\Awesome; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\I18n\I18nApi; class Awesome { private $ID = 'awesome'; private $ZH_CN_URL = 'https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css'; private $DEFAULT_URL = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; public function __construct() { } public function filter() { ?>
<link rel="stylesheet" href="<?php echo $this->getUrl(); ?>">
<?php
} private function getUrl() { switch (I18nApi::getClientLang()) { case 'zh-CN': return $this->ZH_CN_URL; } return $this->DEFAULT_URL; } }
namespace InnStudio\Prober\Timezone; use InnStudio\Prober\Events\Api as Events; class Timezone { public function __construct() { Events::on('init', array($this, 'filter')); Events::on('fetch', array($this, 'filter')); } public function filter() { if ( ! \ini_get('date.timezone')) { \date_default_timezone_set('GMT'); } } }
namespace InnStudio\Prober\ServerStatus; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class ServerStatus { private $ID = 'serverStatus'; public function __construct() { Events::patch('mods', array($this, 'filter')); Events::on('style', array($this, 'filterStyle')); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18nApi::_('Server status'), 'tinyTitle' => I18nApi::_('Status'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="form-group">
<div class="group-label"><?php echo I18nApi::_('System load'); ?></div>
<div class="group-content small-group-container" id="systemLoadAvg"><?php echo Helper::getSysLoadAvg(); ?></div>
</div>
<div class="form-group">
<div class="group-label"><?php echo I18nApi::_('CPU usage'); ?></div>
<div class="group-content small-group-container" id="cpuUsage">
<div class="progress-container">
<div class="number">
<span id="cpuUsagePercent">
10%
</span>
</div>
<div class="progress" id="cpuUsageProgress">
<div id="cpuUsageProgressValue" class="progress-value" style="width: 10%"></div>
</div>
</div>
</div>
</div>
<div class="form-group memory-usage">
<div class="group-label"><?php echo I18nApi::_('Real memory usage'); ?></div>
<div class="group-content">
<div class="progress-container">
<div class="percent" id="memRealUsagePercent"><?php echo $this->getMemUsage('MemRealUsage', true); ?>%</div>
<div class="number">
<span id="memRealUsage">
<?php echo Helper::getHumamMemUsage('MemRealUsage'); ?>
/
<?php echo Helper::getHumamMemUsage('MemTotal'); ?>
</span>
</div>
<div class="progress" id="memRealUsageProgress">
<div id="memRealUsageProgressValue" class="progress-value" style="width: <?php echo $this->getMemUsage('MemRealUsage', true); ?>%"></div>
</div>
</div>
</div>
</div>
<div class="form-group swap-usage">
<div class="group-label"><?php echo I18nApi::_('Real swap usage'); ?></div>
<div class="group-content">
<div class="progress-container">
<div class="percent" id="swapRealUsagePercent"><?php echo $this->getMemUsage('SwapRealUsage', true, 'SwapTotal'); ?>%</div>
<div class="number">
<span id="swapRealUsage">
<?php echo Helper::getHumamMemUsage('SwapRealUsage'); ?>
/
<?php echo Helper::getHumamMemUsage('SwapTotal'); ?>
</span>
</div>
<div class="progress" id="swapRealUsageProgress">
<div id="swapRealUsageProgressValue" class="progress-value" style="width: <?php echo $this->getMemUsage('SwapRealUsage', true, 'SwapTotal'); ?>%"></div>
</div>
</div>
</div>
</div>
<?php
} public function filterStyle() { ?>
<style>
.small-group{
display: inline-block;
background: #eee;
border-radius: 1rem;
margin: 0 .2rem;
padding: 0 1rem;
}
#scriptPath.group-content{
word-break: break-all;
}
</style>
<?php
} private function getMemUsage($key, $precent = false, $totalKey = 'MemTotal') { if (false === $precent) { return Helper::getMemoryUsage($key); } return Helper::getMemoryUsage($key) ? \sprintf('%01.2f', Helper::getMemoryUsage($key) / Helper::getMemoryUsage($totalKey) * 100) : 0; } }
namespace InnStudio\Prober\I18n; class I18nApi { public static function _($str) { static $translation = null; if (null === $translation) { $translation = \json_decode(\base64_decode(\LANG), true); } $clientLang = self::getClientLang(); $output = isset($translation[$clientLang][$str]) ? $translation[$clientLang][$str] : $str; return $output ?: $str; } public static function getClientLang() { static $cache = null; if (null !== $cache) { return $cache; } if ( ! isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { $cache = ''; return $cache; } $client = \explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']); if (isset($client[0])) { $cache = \str_replace('-', '_', $client[0]); } else { $cache = 'en'; } return $cache; } }
namespace InnStudio\Prober\Updater; use InnStudio\Prober\Config\Api as Config; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class Updater { private $ID = 'updater'; public function __construct() { Events::on('script', array($this, 'filter')); Events::on('init', array($this, 'filterInit')); } public function filterInit() { if ( ! Helper::isAction('update')) { return; } if ( ! \is_writable(__FILE__)) { Helper::dieJson(array( 'code' => -1, 'msg' => I18nApi::_('File can not update.'), )); } $content = \file_get_contents(Config::$UPDATE_PHP_URL); if ( ! $content) { Helper::dieJson(array( 'code' => -1, 'msg' => I18nApi::_('Update file not found.'), )); } if ((bool) \file_put_contents(__FILE__, $content)) { Helper::dieJson(array( 'code' => 0, 'msg' => I18nApi::_('Update success...'), )); } Helper::dieJson(array( 'code' => -1, 'msg' => I18nApi::_('Update error.'), )); } public function filter() { $version = Config::$APP_VERSION; $changeLogUrl = Config::$CHANGELOG_URL; $authorUrl = Config::$AUTHOR_URL; echo <<<HTML
<script>
(function(){
var versionCompare = function(left, right) {
if (typeof left + typeof right != 'stringstring')
return false;
var a = left.split('.')
,   b = right.split('.')
,   i = 0, len = Math.max(a.length, b.length);
for (; i < len; i++) {
if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
return 1;
} else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
return -1;
}
}
return 0;
}
checkUpdate();
function update(){
var title = document.querySelector('h1');
title.innerHTML = '<div>⏳ {$this->_('Updating...')}</div>';
var xhr = new XMLHttpRequest();
try {
xhr.open('get', '?action=update');
xhr.send();
xhr.onload = onLoadUpload;
} catch (err) {}
}
function onLoadUpload(){
var xhr = this;
var msg = '';
if (xhr.readyState === 4) {
if (xhr.status >= 200 && xhr.status < 400) {
var res = xhr.responseText;
try {
res = JSON.parse(res)
} catch (err){ }
if (res && res.code === 0) {
msg = '✔️ ' + res.msg;
location.reload();
} else if (res && res.code) {
msg = '❌ ' + res.msg;
} else {
msg = '❌ ' + res;
}
var title = document.querySelector('h1');
title.innerHTML = '<div>' + msg + '</div>';
} else {
title.innerHTML = '❌ {$this->_('Update error')}';
}
}
}
function checkUpdate() {
var version = "{$version}";
var xhr = new XMLHttpRequest();
xhr.open('get', '{$changeLogUrl}');
xhr.send();
xhr.onload = onLoadCheckUpdate;
}
function onLoadCheckUpdate() {
let xhr = this;
if (xhr.readyState === 4 ) {
if (xhr.status >= 200 && xhr.status < 400) {
var data = xhr.responseText;
if (! data) {
return;
}
var versionInfo = getVersionInfo(data);
if (!versionInfo.length) {
return;
}
if (versionCompare('{$version}', versionInfo[0]) === -1) {
var lang = '✨ {$this->_('{APP_NAME} found update! Version {APP_OLD_VERSION} &rarr; {APP_NEW_VERSION}')}';
lang = lang.replace('{APP_NAME}', '{$this->_(Config::$APP_NAME)}');
lang = lang.replace('{APP_OLD_VERSION}', '{$version}');
lang = lang.replace('{APP_NEW_VERSION}', versionInfo[0]);
var updateLink = document.createElement('a');
updateLink.addEventListener('click', update);
updateLink.innerHTML = lang;
var title = document.querySelector('h1');
title.innerHTML = '';
title.appendChild(updateLink);
}
}
}
}
function getVersionInfo(data){
var reg = /^#{2}\s+(\d+\.\d+\.\d+)\s+\-\s+(\d{4}\-\d+\-\d+)/mg;
return reg.test(data) ? [RegExp.$1,RegExp.$2]: [];
}
})()
</script>
HTML;
} private function _($str) { return I18nApi::_($str); } }
namespace InnStudio\Prober\NetworkStats; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class NetworkStats { private $ID = 'networkStats'; public function __construct() { Helper::isWin() || Events::on('style', array($this, 'filterStyle')); Helper::isWin() || Events::patch('mods', array($this, 'filter'), 100); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18nApi::_('Network stats'), 'tinyTitle' => I18nApi::_('Net'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} public function filterStyle() { ?>
<style>
.network-stats-container > *{
float: left;
width: 50%;
text-align: center;
}
</style>
<?php
} private function getContent() { $items = array(); $stats = Helper::getNetworkStats(); if ( ! \is_array($stats)) { return '<div>' . Helper::getNetworkStats() . '</div>'; } foreach (Helper::getNetworkStats() as $ethName => $item) { $rxHuman = Helper::formatBytes($item['rx']); $txHuman = Helper::formatBytes($item['tx']); $items[] = array( 'label' => $ethName, 'content' => <<<HTML
<div class="network-stats-container">
<div class="rx">
<div><span id="network-{$ethName}-rx-total">{$rxHuman}</span></div>
<div><span class="icon">▼</span><span id="network-{$ethName}-rx-rate">0</span><span class="second">/s</span></div>
</div>
<div class="tx">
<div><span id="network-{$ethName}-tx-total">{$txHuman}</span></div>
<div><span class="icon">▲</span><span id="network-{$ethName}-tx-rate">0</span><span class="second">/s</span></div>
</div>
</div>
HTML
); } $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-1'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; $content .= <<<HTML
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$title} {$id}>{$item['content']}</div>
</div>
</div>
HTML;
} return $content; } }
namespace InnStudio\Prober\Config; class Api { public static $APP_VERSION = '1.7.2'; public static $APP_NAME = 'X Prober'; public static $APP_URL = 'https://github.com/kmvan/x-prober'; public static $AUTHOR_URL = 'https://inn-studio.com/prober'; public static $UPDATE_PHP_URL = 'https://raw.githubusercontent.com/kmvan/x-prober/master/dist/prober.php'; public static $AUTHOR_NAME = 'INN STUDIO'; public static $CHANGELOG_URL = 'https://raw.githubusercontent.com/kmvan/x-prober/master/CHANGELOG.md'; }
namespace InnStudio\Prober\Style; use InnStudio\Prober\Events\Api as Events; class Style { private $ID = 'style'; public function __construct() { Events::on('style', array($this, 'filter')); } public function filter() { $this->styleProgress(); $this->styleGlobal(); $this->stylePoiContainer(); $this->stylePoiGrid(); $this->styleTitle(); } private function styleTitle() { ?>
<style>
.long-title{
text-transform: capitalize;
}
.tiny-title{
display: none;
}
</style>
<?php
} private function styleProgress() { ?>
<style>
.progress-container{
position: relative;
}
.progress-container .percent,
.progress-container .number{
position: absolute;
right: 1rem;
bottom: 0;
z-index: 1;
font-weight: bold;
color: #fff;
text-shadow: 0 1px 1px #000;
line-height: 2rem;
}
.progress-container .percent{
left: 1rem;
right: auto;
}
.progress {
position: relative;
display: block;
width: 100%;
height: 2rem;
background: #444;
border-radius: 1rem;
box-shadow: inset 0px 10px 20px rgba(0,0,0,0.3);
}
.progress .progress-value{
position: absolute;
top: .35rem;
bottom: .35rem;
left: .35rem;
right: .35rem;
-webkit-transition: 2s all;
transition: 2s all;
border-radius: 1rem;
background: #00cc00;
box-shadow: inset 0 -5px 10px rgba(0,0,0,0.4), 0 5px 10px 0px rgba(0,0,0,0.3)
}
.progress.medium-low .progress-value{
background: #009999;
}
.progress.medium .progress-value{
background: #f07746;
}
.progress.high .progress-value{
background: #ef2d2d;
}
</style>
<?php
} private function styleGlobal() { ?>
<style>
*{
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
vertical-align: middle;
}
html{
font-size: 75%;
background: #333;
}
body{
background: #f8f8f8;
color: #666;
font-family: "Microsoft YaHei UI", "Microsoft YaHei", sans-serif;
border: 10px solid #333;
margin: 0;
border-radius: 2rem;
line-height: 2rem;
}
a{
cursor: pointer;
color: #333;
text-decoration: none;
}
a:hover,
a:active{
color: #999;
text-decoration: underline;
}
.ini-ok{
color: green;
font-weight: bold;
}
.ini-error{
color: red;
font-weight: bold;
}
h1{
text-align: center;
font-size: 1rem;
background: #333;
border-radius: 0 0 10rem 10rem;
width: 60%;
line-height: 1.5rem;
margin: 0 auto 1rem;
}
h1 *{
display: block;
color: #fff;
padding: 0 0 10px;
}
h1 *:hover{
color: #fff;
}
.form-group{
overflow: hidden;
display: table;
width: 100%;
border-bottom: 1px solid #eee;
min-height: 2rem;
}
.form-group:hover{
background: rgba(0,0,0,.03);
}
.group-label,
.group-content{
display: table-cell;
padding: .2rem .5rem;
}
.group-label{
width: 8rem;
text-align: left;
font-weight: normal;
}
.group-label a{
display: block;
}
.group-content a{
line-height: 1;
display: block;
}
@media (min-width:768px){
.group-label{
width: 15rem;
}
.group-label,
.group-content{
display: table-cell;
padding: .5rem 1rem;
}
}
fieldset{
position: relative;
border: 5px solid #eee;
border-radius: .5rem;
padding: 0;
background: rgba(255,255,255,.5);
margin-bottom: 1rem;
padding: .5rem 0;
}
legend{
background: #333;
margin-left: 1rem;
padding: .5rem 2rem;
border-radius: 5rem;
color: #fff;
margin: 0 auto;
}
p{
margin: 0 0 1rem;
}
.description{
margin: 0;
padding-left: 1rem;
font-style: italic;
}
</style>
<?php
} private function stylePoiContainer() { ?>
<style>
@media (min-width:768px){.poi-container{margin-left:auto;margin-right:auto;padding-left:.5rem;padding-right:.5rem}}
@media (min-width:579px){.poi-container{width:559px}}
@media (min-width:768px){.poi-container{width:748px}}
@media (min-width:992px){.poi-container{width:940px;padding-left:1rem;padding-right:1rem}}
@media (min-width:1200px){.poi-container{width:1180px}}
@media (min-width:992px){.row{margin-left:-.5rem;margin-right:-.5rem}}
</style>
<?php
} private function stylePoiGrid() { ?>
<style>
.row:after{
content: '';
display: block;
clear: both;
}
.row>*{max-width:100%;float:left;width:100%;box-sizing:border-box;padding-left:.25rem;padding-right:.25rem;min-height: 2rem;}
@media (min-width:992px){.row>*{padding-left:.5rem;padding-right:.5rem}}
.poi-g-1-1{width:100%}.poi-g-1-2{width:50%}.poi-g-2-2{width:100%}.poi-g-1-3{width:33.33333%}.poi-g-2-3{width:66.66667%}.poi-g-3-3{width:100%}.poi-g-1-4{width:25%}.poi-g-2-4{width:50%}.poi-g-3-4{width:75%}.poi-g-4-4{width:100%}.poi-g-1-5{width:20%}.poi-g-2-5{width:40%}.poi-g-3-5{width:60%}.poi-g-4-5{width:80%}.poi-g-5-5{width:100%}.poi-g-1-6{width:16.66667%}.poi-g-2-6{width:33.33333%}.poi-g-3-6{width:50%}.poi-g-4-6{width:66.66667%}.poi-g-5-6{width:83.33333%}.poi-g-6-6{width:100%}.poi-g-1-7{width:14.28571%}.poi-g-2-7{width:28.57143%}.poi-g-3-7{width:42.85714%}.poi-g-4-7{width:57.14286%}.poi-g-5-7{width:71.42857%}.poi-g-6-7{width:85.71429%}.poi-g-7-7{width:100%}.poi-g-1-8{width:12.5%}.poi-g-2-8{width:25%}.poi-g-3-8{width:37.5%}.poi-g-4-8{width:50%}.poi-g-5-8{width:62.5%}.poi-g-6-8{width:75%}.poi-g-7-8{width:87.5%}.poi-g-8-8{width:100%}.poi-g-1-9{width:11.11111%}.poi-g-2-9{width:22.22222%}.poi-g-3-9{width:33.33333%}.poi-g-4-9{width:44.44444%}.poi-g-5-9{width:55.55556%}.poi-g-6-9{width:66.66667%}.poi-g-7-9{width:77.77778%}.poi-g-8-9{width:88.88889%}.poi-g-9-9{width:100%}.poi-g-1-10{width:10%}.poi-g-2-10{width:20%}.poi-g-3-10{width:30%}.poi-g-4-10{width:40%}.poi-g-5-10{width:50%}.poi-g-6-10{width:60%}.poi-g-7-10{width:70%}.poi-g-8-10{width:80%}.poi-g-9-10{width:90%}.poi-g-10-10{width:100%}.poi-g-1-11{width:9.09091%}.poi-g-2-11{width:18.18182%}.poi-g-3-11{width:27.27273%}.poi-g-4-11{width:36.36364%}.poi-g-5-11{width:45.45455%}.poi-g-6-11{width:54.54545%}.poi-g-7-11{width:63.63636%}.poi-g-8-11{width:72.72727%}.poi-g-9-11{width:81.81818%}.poi-g-10-11{width:90.90909%}.poi-g-11-11{width:100%}.poi-g-1-12{width:8.33333%}.poi-g-2-12{width:16.66667%}.poi-g-3-12{width:25%}.poi-g-4-12{width:33.33333%}.poi-g-5-12{width:41.66667%}.poi-g-6-12{width:50%}.poi-g-7-12{width:58.33333%}.poi-g-8-12{width:66.66667%}.poi-g-9-12{width:75%}.poi-g-10-12{width:83.33333%}.poi-g-11-12{width:91.66667%}.poi-g-12-12{width:100%}@media (min-width:579px){.poi-g-sm-1-1{width:100%}.poi-g-sm-1-2{width:50%}.poi-g-sm-2-2{width:100%}.poi-g-sm-1-3{width:33.33333%}.poi-g-sm-2-3{width:66.66667%}.poi-g-sm-3-3{width:100%}.poi-g-sm-1-4{width:25%}.poi-g-sm-2-4{width:50%}.poi-g-sm-3-4{width:75%}.poi-g-sm-4-4{width:100%}.poi-g-sm-1-5{width:20%}.poi-g-sm-2-5{width:40%}.poi-g-sm-3-5{width:60%}.poi-g-sm-4-5{width:80%}.poi-g-sm-5-5{width:100%}.poi-g-sm-1-6{width:16.66667%}.poi-g-sm-2-6{width:33.33333%}.poi-g-sm-3-6{width:50%}.poi-g-sm-4-6{width:66.66667%}.poi-g-sm-5-6{width:83.33333%}.poi-g-sm-6-6{width:100%}.poi-g-sm-1-7{width:14.28571%}.poi-g-sm-2-7{width:28.57143%}.poi-g-sm-3-7{width:42.85714%}.poi-g-sm-4-7{width:57.14286%}.poi-g-sm-5-7{width:71.42857%}.poi-g-sm-6-7{width:85.71429%}.poi-g-sm-7-7{width:100%}.poi-g-sm-1-8{width:12.5%}.poi-g-sm-2-8{width:25%}.poi-g-sm-3-8{width:37.5%}.poi-g-sm-4-8{width:50%}.poi-g-sm-5-8{width:62.5%}.poi-g-sm-6-8{width:75%}.poi-g-sm-7-8{width:87.5%}.poi-g-sm-8-8{width:100%}.poi-g-sm-1-9{width:11.11111%}.poi-g-sm-2-9{width:22.22222%}.poi-g-sm-3-9{width:33.33333%}.poi-g-sm-4-9{width:44.44444%}.poi-g-sm-5-9{width:55.55556%}.poi-g-sm-6-9{width:66.66667%}.poi-g-sm-7-9{width:77.77778%}.poi-g-sm-8-9{width:88.88889%}.poi-g-sm-9-9{width:100%}.poi-g-sm-1-10{width:10%}.poi-g-sm-2-10{width:20%}.poi-g-sm-3-10{width:30%}.poi-g-sm-4-10{width:40%}.poi-g-sm-5-10{width:50%}.poi-g-sm-6-10{width:60%}.poi-g-sm-7-10{width:70%}.poi-g-sm-8-10{width:80%}.poi-g-sm-9-10{width:90%}.poi-g-sm-10-10{width:100%}.poi-g-sm-1-11{width:9.09091%}.poi-g-sm-2-11{width:18.18182%}.poi-g-sm-3-11{width:27.27273%}.poi-g-sm-4-11{width:36.36364%}.poi-g-sm-5-11{width:45.45455%}.poi-g-sm-6-11{width:54.54545%}.poi-g-sm-7-11{width:63.63636%}.poi-g-sm-8-11{width:72.72727%}.poi-g-sm-9-11{width:81.81818%}.poi-g-sm-10-11{width:90.90909%}.poi-g-sm-11-11{width:100%}.poi-g-sm-1-12{width:8.33333%}.poi-g-sm-2-12{width:16.66667%}.poi-g-sm-3-12{width:25%}.poi-g-sm-4-12{width:33.33333%}.poi-g-sm-5-12{width:41.66667%}.poi-g-sm-6-12{width:50%}.poi-g-sm-7-12{width:58.33333%}.poi-g-sm-8-12{width:66.66667%}.poi-g-sm-9-12{width:75%}.poi-g-sm-10-12{width:83.33333%}.poi-g-sm-11-12{width:91.66667%}.poi-g-sm-12-12{width:100%}}@media (min-width:768px){.poi-g-md-1-1{width:100%}.poi-g-md-1-2{width:50%}.poi-g-md-2-2{width:100%}.poi-g-md-1-3{width:33.33333%}.poi-g-md-2-3{width:66.66667%}.poi-g-md-3-3{width:100%}.poi-g-md-1-4{width:25%}.poi-g-md-2-4{width:50%}.poi-g-md-3-4{width:75%}.poi-g-md-4-4{width:100%}.poi-g-md-1-5{width:20%}.poi-g-md-2-5{width:40%}.poi-g-md-3-5{width:60%}.poi-g-md-4-5{width:80%}.poi-g-md-5-5{width:100%}.poi-g-md-1-6{width:16.66667%}.poi-g-md-2-6{width:33.33333%}.poi-g-md-3-6{width:50%}.poi-g-md-4-6{width:66.66667%}.poi-g-md-5-6{width:83.33333%}.poi-g-md-6-6{width:100%}.poi-g-md-1-7{width:14.28571%}.poi-g-md-2-7{width:28.57143%}.poi-g-md-3-7{width:42.85714%}.poi-g-md-4-7{width:57.14286%}.poi-g-md-5-7{width:71.42857%}.poi-g-md-6-7{width:85.71429%}.poi-g-md-7-7{width:100%}.poi-g-md-1-8{width:12.5%}.poi-g-md-2-8{width:25%}.poi-g-md-3-8{width:37.5%}.poi-g-md-4-8{width:50%}.poi-g-md-5-8{width:62.5%}.poi-g-md-6-8{width:75%}.poi-g-md-7-8{width:87.5%}.poi-g-md-8-8{width:100%}.poi-g-md-1-9{width:11.11111%}.poi-g-md-2-9{width:22.22222%}.poi-g-md-3-9{width:33.33333%}.poi-g-md-4-9{width:44.44444%}.poi-g-md-5-9{width:55.55556%}.poi-g-md-6-9{width:66.66667%}.poi-g-md-7-9{width:77.77778%}.poi-g-md-8-9{width:88.88889%}.poi-g-md-9-9{width:100%}.poi-g-md-1-10{width:10%}.poi-g-md-2-10{width:20%}.poi-g-md-3-10{width:30%}.poi-g-md-4-10{width:40%}.poi-g-md-5-10{width:50%}.poi-g-md-6-10{width:60%}.poi-g-md-7-10{width:70%}.poi-g-md-8-10{width:80%}.poi-g-md-9-10{width:90%}.poi-g-md-10-10{width:100%}.poi-g-md-1-11{width:9.09091%}.poi-g-md-2-11{width:18.18182%}.poi-g-md-3-11{width:27.27273%}.poi-g-md-4-11{width:36.36364%}.poi-g-md-5-11{width:45.45455%}.poi-g-md-6-11{width:54.54545%}.poi-g-md-7-11{width:63.63636%}.poi-g-md-8-11{width:72.72727%}.poi-g-md-9-11{width:81.81818%}.poi-g-md-10-11{width:90.90909%}.poi-g-md-11-11{width:100%}.poi-g-md-1-12{width:8.33333%}.poi-g-md-2-12{width:16.66667%}.poi-g-md-3-12{width:25%}.poi-g-md-4-12{width:33.33333%}.poi-g-md-5-12{width:41.66667%}.poi-g-md-6-12{width:50%}.poi-g-md-7-12{width:58.33333%}.poi-g-md-8-12{width:66.66667%}.poi-g-md-9-12{width:75%}.poi-g-md-10-12{width:83.33333%}.poi-g-md-11-12{width:91.66667%}.poi-g-md-12-12{width:100%}}@media (min-width:992px){.poi-g-lg-1-1{width:100%}.poi-g-lg-1-2{width:50%}.poi-g-lg-2-2{width:100%}.poi-g-lg-1-3{width:33.33333%}.poi-g-lg-2-3{width:66.66667%}.poi-g-lg-3-3{width:100%}.poi-g-lg-1-4{width:25%}.poi-g-lg-2-4{width:50%}.poi-g-lg-3-4{width:75%}.poi-g-lg-4-4{width:100%}.poi-g-lg-1-5{width:20%}.poi-g-lg-2-5{width:40%}.poi-g-lg-3-5{width:60%}.poi-g-lg-4-5{width:80%}.poi-g-lg-5-5{width:100%}.poi-g-lg-1-6{width:16.66667%}.poi-g-lg-2-6{width:33.33333%}.poi-g-lg-3-6{width:50%}.poi-g-lg-4-6{width:66.66667%}.poi-g-lg-5-6{width:83.33333%}.poi-g-lg-6-6{width:100%}.poi-g-lg-1-7{width:14.28571%}.poi-g-lg-2-7{width:28.57143%}.poi-g-lg-3-7{width:42.85714%}.poi-g-lg-4-7{width:57.14286%}.poi-g-lg-5-7{width:71.42857%}.poi-g-lg-6-7{width:85.71429%}.poi-g-lg-7-7{width:100%}.poi-g-lg-1-8{width:12.5%}.poi-g-lg-2-8{width:25%}.poi-g-lg-3-8{width:37.5%}.poi-g-lg-4-8{width:50%}.poi-g-lg-5-8{width:62.5%}.poi-g-lg-6-8{width:75%}.poi-g-lg-7-8{width:87.5%}.poi-g-lg-8-8{width:100%}.poi-g-lg-1-9{width:11.11111%}.poi-g-lg-2-9{width:22.22222%}.poi-g-lg-3-9{width:33.33333%}.poi-g-lg-4-9{width:44.44444%}.poi-g-lg-5-9{width:55.55556%}.poi-g-lg-6-9{width:66.66667%}.poi-g-lg-7-9{width:77.77778%}.poi-g-lg-8-9{width:88.88889%}.poi-g-lg-9-9{width:100%}.poi-g-lg-1-10{width:10%}.poi-g-lg-2-10{width:20%}.poi-g-lg-3-10{width:30%}.poi-g-lg-4-10{width:40%}.poi-g-lg-5-10{width:50%}.poi-g-lg-6-10{width:60%}.poi-g-lg-7-10{width:70%}.poi-g-lg-8-10{width:80%}.poi-g-lg-9-10{width:90%}.poi-g-lg-10-10{width:100%}.poi-g-lg-1-11{width:9.09091%}.poi-g-lg-2-11{width:18.18182%}.poi-g-lg-3-11{width:27.27273%}.poi-g-lg-4-11{width:36.36364%}.poi-g-lg-5-11{width:45.45455%}.poi-g-lg-6-11{width:54.54545%}.poi-g-lg-7-11{width:63.63636%}.poi-g-lg-8-11{width:72.72727%}.poi-g-lg-9-11{width:81.81818%}.poi-g-lg-10-11{width:90.90909%}.poi-g-lg-11-11{width:100%}.poi-g-lg-1-12{width:8.33333%}.poi-g-lg-2-12{width:16.66667%}.poi-g-lg-3-12{width:25%}.poi-g-lg-4-12{width:33.33333%}.poi-g-lg-5-12{width:41.66667%}.poi-g-lg-6-12{width:50%}.poi-g-lg-7-12{width:58.33333%}.poi-g-lg-8-12{width:66.66667%}.poi-g-lg-9-12{width:75%}.poi-g-lg-10-12{width:83.33333%}.poi-g-lg-11-12{width:91.66667%}.poi-g-lg-12-12{width:100%}}@media (min-width:1200px){.poi-g-xl-1-1{width:100%}.poi-g-xl-1-2{width:50%}.poi-g-xl-2-2{width:100%}.poi-g-xl-1-3{width:33.33333%}.poi-g-xl-2-3{width:66.66667%}.poi-g-xl-3-3{width:100%}.poi-g-xl-1-4{width:25%}.poi-g-xl-2-4{width:50%}.poi-g-xl-3-4{width:75%}.poi-g-xl-4-4{width:100%}.poi-g-xl-1-5{width:20%}.poi-g-xl-2-5{width:40%}.poi-g-xl-3-5{width:60%}.poi-g-xl-4-5{width:80%}.poi-g-xl-5-5{width:100%}.poi-g-xl-1-6{width:16.66667%}.poi-g-xl-2-6{width:33.33333%}.poi-g-xl-3-6{width:50%}.poi-g-xl-4-6{width:66.66667%}.poi-g-xl-5-6{width:83.33333%}.poi-g-xl-6-6{width:100%}.poi-g-xl-1-7{width:14.28571%}.poi-g-xl-2-7{width:28.57143%}.poi-g-xl-3-7{width:42.85714%}.poi-g-xl-4-7{width:57.14286%}.poi-g-xl-5-7{width:71.42857%}.poi-g-xl-6-7{width:85.71429%}.poi-g-xl-7-7{width:100%}.poi-g-xl-1-8{width:12.5%}.poi-g-xl-2-8{width:25%}.poi-g-xl-3-8{width:37.5%}.poi-g-xl-4-8{width:50%}.poi-g-xl-5-8{width:62.5%}.poi-g-xl-6-8{width:75%}.poi-g-xl-7-8{width:87.5%}.poi-g-xl-8-8{width:100%}.poi-g-xl-1-9{width:11.11111%}.poi-g-xl-2-9{width:22.22222%}.poi-g-xl-3-9{width:33.33333%}.poi-g-xl-4-9{width:44.44444%}.poi-g-xl-5-9{width:55.55556%}.poi-g-xl-6-9{width:66.66667%}.poi-g-xl-7-9{width:77.77778%}.poi-g-xl-8-9{width:88.88889%}.poi-g-xl-9-9{width:100%}.poi-g-xl-1-10{width:10%}.poi-g-xl-2-10{width:20%}.poi-g-xl-3-10{width:30%}.poi-g-xl-4-10{width:40%}.poi-g-xl-5-10{width:50%}.poi-g-xl-6-10{width:60%}.poi-g-xl-7-10{width:70%}.poi-g-xl-8-10{width:80%}.poi-g-xl-9-10{width:90%}.poi-g-xl-10-10{width:100%}.poi-g-xl-1-11{width:9.09091%}.poi-g-xl-2-11{width:18.18182%}.poi-g-xl-3-11{width:27.27273%}.poi-g-xl-4-11{width:36.36364%}.poi-g-xl-5-11{width:45.45455%}.poi-g-xl-6-11{width:54.54545%}.poi-g-xl-7-11{width:63.63636%}.poi-g-xl-8-11{width:72.72727%}.poi-g-xl-9-11{width:81.81818%}.poi-g-xl-10-11{width:90.90909%}.poi-g-xl-11-11{width:100%}.poi-g-xl-1-12{width:8.33333%}.poi-g-xl-2-12{width:16.66667%}.poi-g-xl-3-12{width:25%}.poi-g-xl-4-12{width:33.33333%}.poi-g-xl-5-12{width:41.66667%}.poi-g-xl-6-12{width:50%}.poi-g-xl-7-12{width:58.33333%}.poi-g-xl-8-12{width:66.66667%}.poi-g-xl-9-12{width:75%}.poi-g-xl-10-12{width:83.33333%}.poi-g-xl-11-12{width:91.66667%}.poi-g-xl-12-12{width:100%}}
</style>
<?php
} }
namespace InnStudio\Prober\Fetch; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; class Fetch { public function __construct() { if (Helper::isAction('fetch')) { Events::emit('fetch'); $this->outputItems(); } } private function getServerUtcTime() { return \gmdate('Y/m/d H:i:s'); } private function getServerLocalTime() { return \date('Y/m/d H:i:s'); } private function getItems() { return array( 'utcTime' => $this->getServerUtcTime(), 'serverInfo' => array( 'time' => Helper::getServerTime(), 'upTime' => Helper::getServerUpTime(), ), 'cpuUsage' => Helper::getHumanCpuUsage(), 'sysLoadAvg' => Helper::getSysLoadAvg(), 'memTotal' => Helper::getMemoryUsage('MemTotal'), 'memRealUsage' => array( 'percent' => Helper::getMemoryUsage('MemRealUsage') ? \sprintf('%01.2f', Helper::getMemoryUsage('MemRealUsage') / Helper::getMemoryUsage('MemTotal') * 100) : 0, 'number' => Helper::getHumamMemUsage('MemRealUsage') . ' / ' . Helper::getHumamMemUsage('MemTotal'), 'current' => Helper::getMemoryUsage('MemRealUsage'), ), 'swapRealUsage' => array( 'percent' => Helper::getMemoryUsage('SwapRealUsage') ? \sprintf('%01.2f', Helper::getMemoryUsage('SwapRealUsage') / Helper::getMemoryUsage('SwapTotal') * 100) : 0, 'number' => Helper::getHumamMemUsage('SwapRealUsage') . ' / ' . Helper::getHumamMemUsage('SwapTotal'), 'current' => Helper::getMemoryUsage('SwapRealUsage'), ), 'networkStats' => Helper::getNetworkStats(), ); } private function outputItems() { Helper::dieJson(array( 'code' => 0, 'data' => $this->getItems(), )); } }
namespace InnStudio\Prober\ServerInfo; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class ServerInfo { private $ID = 'serverInfo'; public function __construct() { Events::patch('mods', array($this, 'filter'), 200); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18nApi::_('Server information'), 'tinyTitle' => I18nApi::_('Info'), 'display' => array($this, 'display'), ); return $mods; } public function display() { echo <<<HTML
<div class="row">
{$this->getContent()}
</div>
HTML;
} private function getDiskInfo() { if ( ! Helper::getDiskTotalSpace()) { return I18nApi::_('Unavailable'); } $percent = \sprintf('%01.2f', (1 - (Helper::getDiskFreeSpace() / Helper::getDiskTotalSpace())) * 100); $hunamUsed = Helper::formatBytes(Helper::getDiskTotalSpace() - Helper::getDiskFreeSpace()); $hunamTotal = Helper::getDiskTotalSpace(true); return <<<HTML
<div class="progress-container">
<div class="percent" id="diskUsagePercent">{$percent}%</div>
<div class="number">
<span id="diskUsage">
{$hunamUsed} / {$hunamTotal}
</span>
</div>
<div class="progress" id="diskUsageProgress">
<div id="diskUsageProgressValue" class="progress-value" style="width: {$percent}%"></div>
</div>
</div>
HTML;
} private function getContent() { $items = array( array( 'label' => $this->_('Server name'), 'content' => $this->getServerInfo('SERVER_NAME'), ), array( 'id' => 'serverInfoTime', 'label' => $this->_('Server time'), 'content' => Helper::getServerTime(), ), array( 'id' => 'serverUpTime', 'label' => $this->_('Server uptime'), 'content' => Helper::getServerUpTime(), ), array( 'label' => $this->_('Server IP'), 'content' => $this->getServerInfo('SERVER_ADDR'), ), array( 'label' => $this->_('Server software'), 'content' => $this->getServerInfo('SERVER_SOFTWARE'), ), array( 'label' => $this->_('PHP version'), 'content' => \PHP_VERSION, ), array( 'col' => '1-1', 'label' => $this->_('CPU model'), 'content' => Helper::getCpuModel(), ), array( 'col' => '1-1', 'label' => $this->_('Server OS'), 'content' => \php_uname(), ), array( 'id' => 'scriptPath', 'col' => '1-1', 'label' => $this->_('Script path'), 'content' => __FILE__, ), array( 'col' => '1-1', 'label' => $this->_('Disk usage'), 'content' => $this->getDiskInfo(), ), ); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; $content .= <<<HTML
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$id} {$title}>{$item['content']}</div>
</div>
</div>
HTML;
} return $content; } private function _($str) { return I18nApi::_($str); } private function getServerInfo($key) { return isset($_SERVER[$key]) ? $_SERVER[$key] : ''; } }
namespace InnStudio\Prober\ServerBenchmark; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\I18n\I18nApi; class ServerBenchmark { private $ID = 'serverBenchmark'; public function __construct() { Events::patch('mods', array($this, 'filter'), 600); Events::on('script', array($this, 'filterJs')); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18nApi::_('Server Benchmark'), 'tinyTitle' => I18nApi::_('Benchmark'), 'display' => array($this, 'display'), ); return $mods; } public function display() { $lang = I18nApi::_('💡 Higher is better. Note: the benchmark marks are not the only criterion for evaluating the quality of a host/server.'); echo <<<HTML
<p class="description">{$lang}</p>
<div class="row">
{$this->getContent()}
</div>
HTML;
} public function filterJs() { ?>
<script>
(function(){
var el = document.getElementById('benchmark-btn');
var errTx = '❌ <?php echo I18nApi::_('Error, click to retry'); ?>';
if (!el) {
return;
}
function getPoints() {
el.innerHTML = '⏳ <?php echo I18nApi::_('Loading...'); ?>';
var xhr = new XMLHttpRequest();
xhr.onload = load;
xhr.open('get', '?action=benchmark');
xhr.send();
}
function load() {
if (this.readyState !== 4) {
return;
}
if (this.status >= 200 && this.status < 400) {
var res = JSON.parse(this.responseText);
var points = 0;
if (res && res.code === 0) {
for (var k in res.data.points) {
points += res.data.points[k];
}
el.innerHTML = '✔️ ' + points.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
} else if (res && res.code) {
el.innerHTML = '⏳ ' + res.msg;
} else {
el.innerHTML = res;
}
} else {
el.innerHTML = errTx;
}
}
el.addEventListener('click', getPoints);
})()
</script>
<?php
} private function getContent() { $items = array( array( 'label' => I18nApi::_('Amazon/EC2'), 'url' => 'https://aws.amazon.com/', 'content' => 3150, ), array( 'label' => I18nApi::_('VPSSERVER/KVM'), 'url' => 'https://www.vpsserver.com/?affcode=32d56f2dd1b6', 'content' => 3125, ), array( 'label' => I18nApi::_('SpartanHost/KVM'), 'url' => 'https://billing.spartanhost.net/aff.php?aff=801', 'content' => 3174, ), array( 'label' => I18nApi::_('Aliyun/ECS'), 'url' => 'https://promotion.aliyun.com/ntms/act/ambassador/sharetouser.html?userCode=0nry1oii&amp;utm_source=0nry1oii', 'content' => 3302, ), array( 'label' => I18nApi::_('Vultr'), 'url' => 'https://www.vultr.com/?ref=7256513', 'content' => 3182, ), array( 'label' => I18nApi::_('RamNode'), 'url' => 'https://clientarea.ramnode.com/aff.php?aff=4143', 'content' => 3131, ), array( 'label' => I18nApi::_('Linode'), 'url' => 'https://www.linode.com/?r=2edf930598b4165760c1da9e77b995bac72f8ad1', 'content' => 3091, ), array( 'label' => I18nApi::_('Tencent'), 'url' => 'https://cloud.tencent.com/', 'content' => 3055, ), array( 'label' => I18nApi::_('AnyNode/HDD'), 'url' => 'https://billing.anynode.net/aff.php?aff=511', 'content' => 2641, ), array( 'label' => I18nApi::_('BandwagonHOST/SSD'), 'url' => 'https://bandwagonhost.com/aff.php?aff=34116', 'content' => 2181, ), ); $itemsOrder = array(); foreach ($items as $item) { $itemsOrder[] = (int) $item['content']; } \array_multisort( $items, \SORT_DESC, \SORT_NUMERIC, $itemsOrder, \SORT_DESC, \SORT_NUMERIC ); \array_unshift( $items, array( 'label' => I18nApi::_('My server'), 'content' => '<a id="benchmark-btn">👆 ' . I18nApi::_('Click to test') . '</a>', ) ); $content = ''; foreach ($items as $item) { $title = ! isset($item['title']) ? '' : <<<HTML
title="{$item['title']}"
HTML;
$col = isset($item['col']) ? $item['col'] : '1-3'; $id = ! isset($item['id']) ? '' : <<<HTML
id="{$item['id']}"
HTML;
$label = ! isset($item['url']) ? $item['label'] : <<<HTML
<a href="{$item['url']}" target="_blank">{$item['label']}</a>
HTML;
$marks = \is_numeric($item['content']) ? \number_format((float) $item['content']) : $item['content']; $content .= <<<HTML
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$label}</div>
<div class="group-content" {$id} {$title}>{$marks}</div>
</div>
</div>
HTML;
} return $content; } }
namespace InnStudio\Prober\Footer; use InnStudio\Prober\Config\Api as Config; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\I18nApi; class Footer { private $ID = 'footer'; public function __construct() { Events::on('footer', array($this, 'filter')); Events::on('style', array($this, 'filterStyle')); } public function filter() { $timer = (\microtime(true) - TIMER) * 1000; ?>
<a href="<?php echo I18nApi::_(Config::$APP_URL); ?>" target="_blank"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/38ef81f8aca64bb9a64448d0d70f1308ef5341ab/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png"></a>
<div class="poi-container">
<div class="footer">
<?php echo \sprintf(I18nApi::_('Generator %s'), '<a href="' . I18nApi::_(Config::$APP_URL) . '" target="_blank">' . I18nApi::_(Config::$APP_NAME) . '</a>'); ?>
/
<?php echo \sprintf(I18nApi::_('Author %s'), '<a href="' . I18nApi::_(Config::$AUTHOR_URL) . '" target="_blank">' . I18nApi::_(Config::$AUTHOR_NAME) . '</a>'); ?>
/
<?php echo Helper::formatBytes(\memory_get_usage()); ?>
/
<?php echo \sprintf('%01.2f', $timer); ?>ms
</div>
</div>
<?php
} public function filterStyle() { ?>
<style>
.footer{
text-align: center;
margin: 2rem auto 5rem;
padding: .5rem 1rem;
}
@media (min-width: 768px) {
.footer{
background: #333;
color: #ccc;
width: 60%;
border-radius: 10rem;
}
.footer a{
color: #fff;
}
}
.footer a:hover{
text-decoration: underline;
}
</style>
<?php
} }new \InnStudio\Prober\Awesome\Awesome();
new \InnStudio\Prober\Benchmark\Benchmark();
new \InnStudio\Prober\Database\Database();
new \InnStudio\Prober\Fetch\Fetch();
new \InnStudio\Prober\Footer\Footer();
new \InnStudio\Prober\MyInfo\MyInfo();
new \InnStudio\Prober\Nav\Nav();
new \InnStudio\Prober\NetworkStats\NetworkStats();
new \InnStudio\Prober\PhpExtensionInfo\PhpExtensionInfo();
new \InnStudio\Prober\PhpInfo\PhpInfo();
new \InnStudio\Prober\PhpInfoDetail\PhpInfoDetail();
new \InnStudio\Prober\Script\Script();
new \InnStudio\Prober\ServerBenchmark\ServerBenchmark();
new \InnStudio\Prober\ServerInfo\ServerInfo();
new \InnStudio\Prober\ServerStatus\ServerStatus();
new \InnStudio\Prober\Style\Style();
new \InnStudio\Prober\Timezone\Timezone();
new \InnStudio\Prober\Updater\Updater();
new \InnStudio\Prober\Entry\Entry();