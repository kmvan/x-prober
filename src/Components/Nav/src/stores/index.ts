import { configure, makeAutoObservable } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Main {
  public activeIndex = 0
  public constructor() {
    makeAutoObservable(this)
  }
  public setActiveIndex = (activeIndex: number) => {
    this.activeIndex = activeIndex
  }
}
export const NavStore = new Main()
