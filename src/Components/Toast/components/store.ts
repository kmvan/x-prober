import { configure, makeAutoObservable, runInAction } from 'mobx';
import type { ReactNode } from 'react';configure({
  enforceActions: 'observed',
});
class Main {
  isOpen = false;
  msg: ReactNode = '';
  constructor() {
    makeAutoObservable(this);
  }
  setMsg = (msg: ReactNode) => {
    this.msg = msg;
  };
  close = (dalaySeconds = 0) => {
    setTimeout(() => {
      runInAction(() => {
        this.isOpen = false;
      });
    }, dalaySeconds * 1000);
  };
  open = (msg?: ReactNode) => {
    this.msg = msg;
    this.isOpen = true;
  };
}
export const ToastStore = new Main();
