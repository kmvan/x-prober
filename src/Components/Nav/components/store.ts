import { configure, makeAutoObservable } from 'mobx';configure({
  enforceActions: 'observed',
});
class Main {
  activeIndex = 0;
  isOpen = false;
  constructor() {
    makeAutoObservable(this);
  }
  setActiveIndex = (activeIndex: typeof this.activeIndex) => {
    this.activeIndex = activeIndex;
  };
  setIsOpen = (isOpen: typeof this.isOpen) => {
    this.isOpen = isOpen;
  };
}
export const NavStore = new Main();
