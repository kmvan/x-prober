import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '../Utils/components/is-deep-equal/index.ts';
import type { UserConfigProps } from './typings.ts';configure({
  enforceActions: 'observed',
});
class Main {
  data: UserConfigProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (data: UserConfigProps | null) => {
    if (isDeepEqual(data, this.data)) {
      return;
    }
    this.data = data;
  };
}
export const UserConfigStore = new Main();
