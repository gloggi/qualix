# Guiding Principles

[← Technical Documentation](../01 Technical Documentation TOC.md)

This document describes our high-level domain-specific vision for Qualix.

- Qualix is open for all who want to use it. Open source, free to use, forever. We provide an instance at https://qualix.flamberg.ch for easy access.
- Qualix stores very sensitive information about the participants, but even so only stores what's absolutely necessary, and allows course leaders to clean up sensitive data after the course. We don't store the participant's gender or email for this reason.
- We try to follow security best practices and strive to keep the public Qualix instance safe and up to date. We try our best to keep Qualix available and performant during courses, especially in the spring course period.
- In case of privacy or availability concerns, it is possible and encouraged to run your own instance of Qualix. We provide instructions on how to do this in the repository's README.md. We notify instances which we are aware of when there are breaking changes (mostly changed PHP version requirements).
- In accordance with PBS's [IT guidelines](https://itkompass.scout.ch/#/it-guidelines/principles), Qualix primarily tries to solve observation collection and feedback planning for scouting courses. For other tasks, good integration with other tools is preferred over implementing more and more features in Qualix.
- We design the features of Qualix out of our own scouting course experience, but always try to follow the documentation of PBS, especially ["Rückmelden, Qualifizieren und Fördern im Ausbildungskurs"](https://issuu.com/pbs-msds-mss/docs/3118.01de-rqf-20160831-akom) (or the new version which is coming out soon).
- Qualix is optimized for courses who follow the best practices in the literature. Some concrete examples:
  - Observations are short notes, not whole documents. We won't extend the length limit unless we see very compelling reasons to do so. Reason: Self-contained observations ("one statement per observation") are easier to understand and mentally tick off later, discourage over-interpretation, and embed cleanly as evidence quotes in the feedback free-text.
  - Observations should be objective and fact-based, not feelings or interpretation, so they can be used as solid evidence toward participants fulfilling course requirements.
  - There is an enforced limit of maximum 40 requirements per course, and a recommended limit of 10 mandatory requirements per course. Adding more requirements into a course will require too much time (including teaching the topic, letting participants practice, observing them in a clearly defined moment, more rounds of teaching, practicing and second chances for participants who need it).
- Qualix is supposed to be flexible enough to support many different approaches to observations, feedbacks and qualifications. Unused features are silently hidden until they are activated.
- Qualix supports both common qualification methods in scouting courses: The classic "Zweipunkt-Qualifikation" where participants get one big qualification feedback at the end of the course, as well as "fortlaufende Qualifikation" where participants get small qualification feedbacks during the course as soon as they have shown their merit in a requirement. Mixed styles in between the two methods are also supported. See the new version of the RQF brochure linked above for more details.
- Qualix is a tool for the course leaders. The participants should never see Qualix on a screen during a course, or know it's being used.
- Qualix generally requires an internet connection to use. Having said that, it is possible to run an instance of Qualix on a laptop / server / raspberry pi and a wifi router in a course when there is no internet connection. The instructions in the README.md help with that too. In case the internet connection or login session is lost spontaneously, we try to design features such that as little work as possible is lost (e.g. auto-save, form restoring, offline warning). 
- Qualix is optimized for use on laptops, but provides a responsive layout which is reasonably usable on tablets and phones as well.
