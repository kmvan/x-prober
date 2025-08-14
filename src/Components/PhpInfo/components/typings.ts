export interface PhpInfoPollDataProps {
  phpVersion: string;
  sapi: string;
  displayErrors: boolean;
  errorReporting: number;
  memoryLimit: string;
  postMaxSize: string;
  uploadMaxFilesize: string;
  maxInputVars: number;
  maxExecutionTime: number;
  defaultSocketTimeout: number;
  allowUrlFopen: boolean;
  smtp: boolean;
  disableFunctions: string[];
  disableClasses: string[];
}
