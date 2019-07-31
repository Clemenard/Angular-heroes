import { Component, OnInit, Input } from '@angular/core';
import { Hero } from '../hero';
import { ActivatedRoute } from '@angular/router';
import { Location } from '@angular/common';

import { HeroService }  from '../hero.service';

@Component({
  selector: 'app-hero-detail',
  templateUrl: './hero-detail.component.html',
  styleUrls: ['./hero-detail.component.css']
})
export class HeroDetailComponent implements OnInit {
@Input() hero: Hero;
  heroes: Hero[];
constructor(
private route: ActivatedRoute,
private heroService: HeroService,
private location: Location
) {}

ngOnInit(): void {
this.getHero();
this.getOthersHeroes();
}

goBack(): void {
  this.location.back();
}

getHero(): void {
const id = +this.route.snapshot.paramMap.get('id');
this.heroService.getHero(id)
  .subscribe(hero => this.hero = hero);
}

getOthersHeroes(): void {
  const id = +this.route.snapshot.paramMap.get('id');
  this.heroService.getOthersHeroes(id)
      .subscribe(heroes => this.heroes = heroes);
}

save(): void {
   this.heroService.updateHero(this.hero)
     .subscribe(() => this.goBack());
 }



}
